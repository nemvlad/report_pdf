<?php
require_once 'data.php';
require_once 'lib.php';
require_once 'ReportModel.php';

$data = getDataFromService();

$reportModel = new ReportModel();

$reportModel->prepareSummaryData($data);

