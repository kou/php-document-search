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

            $all_text = $entry->title . " ";
            $all_text .= preg_replace("/\n/", " ", $entry->content);
            preg_match_all("/.{1,1024}/u", $all_text, $chunks);
            foreach ($chunks as $chunk) {
                fwrite($pipes[0], $chunk[0]);
                fflush($pipes[0]);
            }
            fclose($pipes[0]);

            $nouns = [];
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
                if (count($components) > 7) {
                    $reading = $components[7];
                } else {
                    $reading = $this->guessReading($surface);
                }

                if (count($nouns) > 0 && $surface == "_") {
                    $nouns[] = [$surface, $reading];
                    continue;
                }

                if ($class != "名詞") {
                    $this->flushContinuousNouns($nouns);
                    $nouns = [];
                    continue;
                }
                if ($subclass1 == "数") {
                    $this->flushContinuousNouns($nouns);
                    $nouns = [];
                    continue;
                }
                if (preg_match("/^[\"'\\-()!=\\[\\]]/u", $surface) ||
                    $surface == "—") {
                    $this->flushContinuousNouns($nouns);
                    $nouns = [];
                    continue;
                }

                if (count($nouns) == 1 &&
                    $this->isAlphabetOnly($nouns[0][0]) &&
                    $this->isAlphabetOnly($surface)) {
                    $this->flushContinuousNouns($nouns);
                    $nouns = [];
                }

                $nouns[] = [$surface, $reading];
            }
            $this->flushContinuousNouns($nouns);
            fclose($pipes[1]);

            proc_close($mecab);
            echo("done\n");
        }
    }

    private function isAlphabetOnly($text)
    {
        return preg_match("/^[a-zA-Z\\\\]+$/", $text);
    }

    private function guessReading($surface)
    {
        if (preg_match("/^[\\p{Katakana}ー]+$/u", $surface)) {
            return $surface;
        } else {
            return null;
        }
    }

    private function flushContinuousNouns($nouns)
    {
        if (count($nouns) == 1) {
            $surface = $nouns[0][0];
            $reading = $nouns[0][1];
            echo("Single noun: <$surface><$reading>\n");
            $this->registerTerm($surface, $reading);
        } else if (count($nouns) > 1) {
            $concatenated_surface = "";
            $concatenated_reading = "";
            foreach ($nouns as $noun) {
                $surface = $noun[0];
                $reading = $noun[1];
                $concatenated_surface .= $surface;
                $concatenated_reading .= $reading;
            }
            echo("Continuous nouns: <$concatenated_surface><$concatenated_reading>\n");
            $this->registerTerm($concatenated_surface, $concatenated_reading);
        }
    }

    private function registerTerm($surface, $reading)
    {
        $label = $surface;
        $surface = strtolower($label);

        if ($this->termExist($surface))
            return;

        $term = new Term();
        $term->term = $surface;
        $term->label = $label;
        if ($reading) {
            $term->reading = $reading;
        } else {
            $term->reading = "";
        }
        $term->save();
        echo("Register: <$surface><$label><$reading>\n");
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
