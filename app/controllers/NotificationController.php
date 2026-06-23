<?php

require_once ROOT_PATH . '/app/models/NotificationModel.php';

class NotificationController
{
    private NotificationModel $model;

    public function __construct()
    {
        $this->model = new NotificationModel();
    }

    public function getNotification()
    {
        return $this->model->getLatestDecision();
    }

    public function test()
    {
        $notification = $this->getNotification();

        include ROOT_PATH . '/app/views/tes_notification.php';
    }
}
