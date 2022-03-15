<?php
namespace PresstoJam;

class Publish {

    private $client;
    private $project_id;
    private $download_dir;

    function __construct($client, $project_id) {
        $this->client = $client;
        $this->project_id = $project_id;
    }

    function withDownload($dir) {
        $this->download_dir = $dir;
    }

    function publish() {
        $response = $this->client->put("/projects", ["id"=>$this->project_id, "process"=>true]);
        sleep(20);
        $complete=false;
        while (!$complete) {
            $res = $this->client->get("/projects-primary", ["id"=>$this->project_id, "__fields"=>["id","process"]]);
            $complete = ($res['process']) ? false : true;
            if ($complete) {
                if ($this->download_dir) {
                    $download = new \Download\Downloader($this->client, $this->project_id);
                    $download->download($this->download_dir);
                }
                exit;
            }
            sleep(2);
        }
        
    }
}