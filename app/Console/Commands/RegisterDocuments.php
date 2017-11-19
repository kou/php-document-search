<?php

namespace App\Console\Commands;

use App\Entry;

use Illuminate\Console\Command;

class RegisterDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'doc:register';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register PHP documents';

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
        $now = new \DateTime();

        foreach (glob('public/doc/*.html') as $html_path) {
            echo("Register: $html_path\n");
            $this->register($html_path);
        }

        /* TODO: Enable me */
        /* foreach (Entry::where('updated_at', '<', $now)->cursor() as $entry) { */
        /*     echo("Remove: $entry->title\n"); */
        /*     $entry->delete(); */
        /* } */
    }

    private function register($html_path)
    {
        $document = new \DOMDocument();
        @$document->loadHTMLFile($html_path);
        $xpath = new \DOMXPath($document);
        $entry = new Entry();
        $entry->url = "/doc/" . basename($html_path);
        $this->extract_title($entry, $xpath);
        $this->extract_content($entry, $xpath);
        $entry->save();
    }

    private function extract_title(Entry $entry, \DOMXPath $xpath)
    {
        $title_node = $xpath->evaluate('/html/head/title/text()')->item(0);
        if (!$title_node) {
            return;
        }

        $entry->title = $title_node->wholeText;
    }

    private function extract_content(Entry $entry, \DOMXPath $xpath)
    {
        $content_texts = $xpath->evaluate('/html/body/div[@class != "manualnavbar"]//text()');
        $content = '';
        $previous_is_empty = false;
        foreach ($content_texts as $text) {
            $trimed_content = trim($text->nodeValue);
            if (empty($trimed_content)) {
                $previous_is_empty = true;
                continue;
            }

            if ($previous_is_empty) {
                $content .= "\n";
            }
            $content .= $trimed_content;
            if (preg_match("/\\n/", $trimed_content)) {
                $content .= "\n";
            }
            $previous_is_empty = false;
        }
        $entry->content = $content;
    }
}
