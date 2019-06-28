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
    public function __construct($url, $apiKey)
    {
        $this->url = $url;
        $this->apiKey = $apiKey;
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

    public function getAllCameras() {
       try {
           $response = Httpful\Request::get($this->url . '/api/2.0/camera?apiKey=' . $this->apiKey)->send();
       } catch (Httpful\Exception\ConnectionErrorException $e) {
          echo "Exception $e";
       }
       if (isset($response)) {
           $this->lastRequest = $response->request;
           $this->lastHeaders = $response->headers;
           $this->lastData = $response->body->data;
           return ($this->lastData);
       }
       return null;
    }

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

    public function getLastRecord($cameraId) {
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

    public function isCameraAlive($cameraId) {

    }

}