<?php

use App\Observers\ModelLogObserver;

function logAction($record, $action){
    // Manually trigger the observer's `show` method
    $observer = new ModelLogObserver();
    $observer->log_action($record, $action);
}
function logShowRecord($record){
    // Manually trigger the observer's `show` method
    $observer = new ModelLogObserver();
    $observer->show($record);
}

function logShowColumn($record, $column){
    // Trigger the observer for a specific column
    $observer = new ModelLogObserver();
    $observer->showColumn($record, $column); // Replace with your column name
}
function logDownloadDocument($record, $column){
    // Trigger the observer for a specific column
    $observer = new ModelLogObserver();
    $observer->downloadedDocument($record, $column); // Replace with your column name
}

function restoreRecord($record){
    $observer = new ModelLogObserver();
    $observer->restoreRecord($record); // Replace with your column name
}
