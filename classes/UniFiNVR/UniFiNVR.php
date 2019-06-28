<?php
/**
 * Created by andrea
 * Date: 27/06/2019
 * Time: 11:51
 */

namespace UniFiNVR;
use DateTime;
use Httpful;

class UniFiNVR
{
    protected $url;
    protected $apiKey;
    protected $lastRequest;
    protected $lastHeaders;
    protected $exitCode;
    protected $lastData;

    /**
     * UniFiNVR constructor.
     * @param string $url
     * @param string $apiKey
     */
    public function __construct(string $url, string $apiKey)
    {
        $this->url = $url;
        $this->apiKey = $apiKey;
        $this->lastRequest = null;
        $this->lastHeaders = null;
        $this->exitCode = null;
        $this->lastData = null;
    }

    private function makeRequest($url) {
        try {
            $response = Httpful\Request::get($url)->send();
            $this->lastRequest = $response->request;
            $this->lastHeaders = $response->headers;
            $this->exitCode = $response->code;
            $this->lastData = $response->body;
        } catch (Httpful\Exception\ConnectionErrorException $e) {
            echo "Exception $e";
            exit(1);
        }
        return ($this->lastData);
    }

    private function getServerInfo() {
        return $this->makeRequest($this->url . '/api/2.0/server/' . '?apiKey=' . $this->apiKey);
    }

    /**
     * @return object|null
     */
    public function getAllCameras() {
        return $this->makeRequest($this->url . '/api/2.0/camera/' . '?apiKey=' . $this->apiKey)->data;
    }

    /**
     * @return false|string
     */
    public function discoveryCameras() {
        $cameras = $this->getAllCameras();
        $results = null;
        foreach ($cameras as $key => $camera){
            $dataItem = [];
            $dataItem['{#ID}'] = $camera->_id;
            $dataItem['{#NAME}'] = $camera->name;
            $dataItem['{#IP}'] = $camera->host;
            $results['data'][] = $dataItem;
        }
        return json_encode($results, JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param string $cameraId
     * @return string|null
     * @throws \Exception
     */
    public function getLastRecord(string $cameraId) {
        $lastRecord = null;
        $response = $this->makeRequest($this->url . '/api/2.0/camera/' . $cameraId . '?apiKey=' . $this->apiKey);
        if(isset($response) && ($this->exitCode == '200')) {
            $epoch = $response->data[0]->lastRecordingStartTime;
            $epoch = DateTime::createFromFormat('U.u', $epoch / 1000)->format('U');
            $now = (new DateTime())->format('U');
            $lastRecord = $now - $epoch;
        }
        return $lastRecord;
    }

    /**
     * @param string $cameraId
     * @return string|null
     */
    public function isCameraAlive(string $cameraId) {
        $state = null;
        $response = $this->makeRequest($this->url . '/api/2.0/camera/' . $cameraId . '?apiKey=' . $this->apiKey);
        if(isset($response) && ($this->exitCode == '200'))
            $state = $response->data[0]->state;
        return $state;
    }

    /**
     * @return int|null
     */
    public function getFreeSpace() {
        return $this->getServerInfo()->data[0]->systemInfo->disk->freeKb;
    }

    /**
     * @return int|null
     */
    public function getUsedSpace() {
        return $this->getServerInfo()->data[0]->systemInfo->disk->usedKb;
    }
}