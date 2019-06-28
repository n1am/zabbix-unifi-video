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
        $this->lastData = null;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return object|null
     */
    public function getAllCameras() {
        try {
           $response = Httpful\Request::get($this->url . '/api/2.0/camera?apiKey=' . $this->apiKey)->send();
           $this->lastRequest = $response->request;
           $this->lastHeaders = $response->headers;
           $this->lastData = $response->body->data;
        } catch (Httpful\Exception\ConnectionErrorException $e) {
          echo "Exception $e";
       }
        return ($this->lastData);
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
            $dataItem['{#MODEL}'] = $camera->model;
            $dataItem['{#IP}'] = $camera->host;
            $results['data'][] = $dataItem;
        }
        return json_encode($results, JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param string $cameraId
     * @return string|null
     */
    public function getLastRecord(string $cameraId) {
        $lastRecord = null;
        try {
            $response = Httpful\Request::get($this->url . '/api/2.0/camera/' . $cameraId . '?apiKey='
                . $this->apiKey)->send();
            $epoch = $response->body->data[0]->lastRecordingStartTime;
            $lastRecord = DateTime::createFromFormat('U.u', $epoch/1000)->format('Y-m-d H:i:s');
        } catch (Httpful\Exception\ConnectionErrorException $e) {
            echo "Exception $e";
        }
        return $lastRecord;
    }

    /**
     * @param string $cameraId
     * @return string|null
     */
    public function isCameraAlive(string $cameraId) {
        $state = null;
        try {
            $response = Httpful\Request::get($this->url . '/api/2.0/camera/' . $cameraId . '?apiKey='
                . $this->apiKey)->send();
            $state = $response->body->data[0]->state;
        } catch (Httpful\Exception\ConnectionErrorException $e) {
            echo "Exception $e";
        }
        return $state;
    }

    /**
     * @return int|null
     */
    public function getFreeSpace() {
        $space = null;
        try {
            $response = Httpful\Request::get($this->url . '/api/2.0/server/' . '?apiKey='
                . $this->apiKey)->send();
            $space = $response->body->data[0]->systemInfo->disk->freeKb;
        } catch (Httpful\Exception\ConnectionErrorException $e) {
            echo "Exception $e";
        }
        return $space;
    }

    /**
     * @return int|null
     */
    public function getUsedSpace() {
        $space = null;
        try {
            $response = Httpful\Request::get($this->url . '/api/2.0/server/' . '?apiKey='
                . $this->apiKey)->send();
            $space = $response->body->data[0]->systemInfo->disk->usedKb;
        } catch (Httpful\Exception\ConnectionErrorException $e) {
            echo "Exception $e";
        }
        return $space;
    }
}