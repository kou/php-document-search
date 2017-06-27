<?php

namespace App\Console\Commands;

use App\Entry;
use App\Term;

use Illuminate\Console\Command;

class RegisterTerms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'term:register';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register terms for auto complete';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (Entry::query()->cursor() as $entry) {
            $descriptor_spec = array(
                0 => array("pipe", "r"),
                1 => array("pipe", "w"),
                2 => array("pipe", "w"),
            );
            $mecab = proc_open('mecab', $descriptor_spec, $pipes);

            fwrite($pipes[0], $entry->title);
            fclose($pipes[0]);

            while (true) {
                $line = rtrim(fgets($pipes[1]));
                if ($line == "EOS") {
                    break;
                }

                $components = explode("\t", $line, 2);
                $surface = $components[0];
                $attributes = $components[1];

                $components = explode(",", $attributes);
                $class = $components[0];
                $subclass1 = $components[1];
                if (count($components) > 8) {
                    $reading = $components[8];
                } else {
                    $reading = null;
                }

                if ($class != "名詞") {
                    continue;
                }
                if ($subclass1 == "数") {
                    continue;
                }
                if (!$reading) {
                    $reading = $this->guessReading($surface);
                }

                if ($this->termExist($surface)) {
                    continue;
                }

                $term = new Term();
                $term->term = $surface;
                if ($reading) {
                    $term->reading = $reading;
                } else {
                    $term->reading = "";
                }
                $term->save();

                echo("Register: <$surface><$class><$subclass1><$reading>\n");
            }
            fclose($pipes[1]);

            proc_close($mecab);
        }
    }

    private function guessReading($surface)
    {
        if (preg_match("/^[\\p{Katakana}\\-]+$/u", $surface)) {
            return $surface;
        } else {
            return null;
        }
    }

    private function termExist($surface)
    {
        return Term::query()
            ->where("term", $surface)
            ->limit(1)
            ->get()
            ->count() == 1;
    }
}
