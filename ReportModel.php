<?php

namespace models\reports;

use \models\SlaModel;
use models\systemsettings\ReportTemplateModel;
use \PHPExcel;
use \PHPExcel_Cell;
use \PHPExcel_IOFactory;
use \PHPExcel_Settings;
use \PHPExcel_Style_Alignment;
use \PHPExcel_Worksheet_PageSetup;
use \TCPDF;
use \TCPDF_FONTS;

require_once __DIR__ . '/../../lib/config.php';

class ReportModel extends SlaModel
{
	private $pieColors = array("#4CAF50", "#F44336", "#81C784", "#E57373", "#43A047", "#E53935", "#1B5E20", "#B71C1C");

	static public function getServiceUrl()
	{
		return 'sys/report';
	}

	/**
	 * Get objects list
	 * @param array $filterData
	 * @param int $page
	 * @param int $limit
	 * @param array $sort
	 * @param array $searchData
	 * @param bool $extendedInfo
	 * @param bool $simpleFilter
	 * @return mixed
	 */
	public function get($filterData = array(), $page = null, $limit = null, $sort = array(), $searchData = array(), $extendedInfo = false, $simpleFilter = true)
	{
		$getVars = array();

		if ($extendedInfo) {
			$getVars['extended_info'] = 1;
		}

		// To get report data by its id we have to pass id within item handler
		if (isset($filterData['id'])) {
			$getVars['id'] = $filterData['id'];
			unset($filterData['id']);
		}

		// Recent request should be at the top of the table
		$sort = array(
			array(
				'property' => 'id',
				'direction' => 'DESC'
			)
		);

		$getVars = array_merge($getVars, $this->buildFilterVars($filterData, $searchData, $simpleFilter), $this->buildPaginationVars($page, $limit), $this->buildSortingVars($sort));

		$this->restApi->SetOpts($this->getServiceUrl(), 'GET', $getVars);
		$result = $this->restApi->execute();

		return $result;
	}

	/**
	 * Build report by its id
	 * @param int|string $reportId - report id
	 * @param string $reportType - report format, supported formats: xls, pdf
	 */
	public function buildReport($reportId, $reportType)
	{
		ini_set('memory_limit', '328M');
		set_time_limit(360);

		// TODO: Handle errors properly!
		if (!in_array($reportType, array('xls', 'pdf'))) {
			echo(gettext('Error: Report type is not supported'));
			die;
		}

		$reportData = $this->get(array('id' => $reportId), null, null, array(), array(), true);

		if ($reportData[self::CLIENT_RESPONSE_SUCCESS_ATTR] != true) {
			echo(gettext('Error ' . $reportData[self::CLIENT_RESPONSE_ERROR_CODE_ATTR] . ': ' . $reportData[self::CLIENT_RESPONSE_MESSAGE_ATTR]));
			die;
		}

		if (empty($reportData[self::CLIENT_RESPONSE_DATA_ATTR]) || empty($reportData[self::CLIENT_RESPONSE_DATA_ATTR][0]->data)) {
			echo(gettext('Error: Empty data set'));
			die;
		}

		$reportData = $reportData[self::CLIENT_RESPONSE_DATA_ATTR][0];
		$reportClassId = $reportData->report_template->report_class;

		switch ($reportClassId) {
			// Summary report
			case 4:
				$summaryReportData = $this->prepareSummaryData($reportData);

				if ($reportType === 'xls')
					$this->generateSummaryReportXLS($summaryReportData);
				elseif ($reportType === 'pdf')
					$this->generateSummaryReportPDF($summaryReportData);
				break;
			// Detail report
			case 5:

				$detailReportData = $this->prepareDetailData($reportData);
				if ($reportType === 'xls')
					$this->generateDetailReportXLS($detailReportData);
				elseif ($reportType === 'pdf')
					$this->generateDetailReportPDF($detailReportData);
				break;
			// Default report
			default:
				$defaultReportData = $this->prepareDefaultData($reportData);
				if ($reportType === 'xls')
					$this->generateDefaultReportXLS($defaultReportData);
				elseif ($reportType === 'pdf')
					$this->generateDefaultReportPDF($defaultReportData);
				break;
		}
	}

	/**
	 * Generate summary report in XLS format
	 * @param $objectsData
	 */
	public function generateSummaryReportXLS($objectsData)
	{
		/**
		 * Generate excel document
		 */
		$documentName = $this->getGeneratedDocumentTitle();

		PHPExcel_Settings::setLocale('ru_ru');

		$excel = new PHPExcel();

		// Set excel document properties
		$excel->getProperties()->setTitle($documentName);

		$excelSheet = $excel->getActiveSheet();

		$summaryColumnsCount = count($objectsData['summary']['thead']);

		$detailThresholdColumns = array();
		if (!empty($objectsData['services_with_errors']['tdata'])) {
			$detailColumnsCount = 0;
			for ($i = 0; $i < count($objectsData['services_with_errors']['tdata'][0]); ++$i) {
				if (is_array($objectsData['services_with_errors']['tdata'][0][$i])) {
					$detailColumnsCount += 2;
					$detailThresholdColumns[] = $i;
				} else {
					++$detailColumnsCount;
				}
			}
		} else {
			$detailColumnsCount = count($objectsData['services_with_errors']['thead']);
		}

		$sheetMaxColumnIndex = $summaryColumnsCount > $detailColumnsCount ? $summaryColumnsCount : $detailColumnsCount;

		// Set excel page properties
		$excelSheet->setPrintGridlines(true);
		$excelSheet->getPageSetup()
			->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
			->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
			->setFitToPage(true)
			->setFitToWidth(1)
			->setFitToHeight(0)
			->setHorizontalCentered(true);

		for ($i = 0; $i < $sheetMaxColumnIndex; ++$i) {
			$excelSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setWidth(20);
		}

		// TODO: Add generator method for style arrays
		// Styles
		$textStyle = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap' => true
			),
			'font' => array(
				'bold' => false,
				'name' => 'Times New Roman',
				'size' => 10
			)
		);
		$redTextStyle = $textStyle;
		$redTextStyle['fill'] = array(
			'type' => \PHPExcel_Style_Fill::FILL_SOLID,
			'color' => array('rgb' => 'F44336')
		);

		$headerStyle = $textStyle;
		$headerStyle['font']['bold'] = true;
		$mediumHeaderStyle = $headerStyle;
		$mediumHeaderStyle['font']['size'] = 14;
		$bigHeaderStyle = $headerStyle;
		$bigHeaderStyle['font']['size'] = 18;

		$allBordersStyle = array(
			'borders' => array(
				'allborders' => array(
					'style' => \PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);

		// Set excel page title
		$excelSheet->setTitle($documentName);


		/**
		 * HEADER
		 */


		$excelSheet->getRowDimension('1')->setRowHeight(60);

		// Header logo
		$headerLogo = new \PHPExcel_Worksheet_Drawing();
		$headerLogo->setName($objectsData['header']);
		$headerLogo->setDescription($objectsData['header']);
		$headerLogo->setPath('resources/images/logo_mo.png');
		$headerLogo->setCoordinates('A1');
		$headerLogo->setWidthAndHeight(60, 80);
		$headerLogo->setWorksheet($excelSheet);

		// Header text
		$excelSheet->mergeCells('B1:' . PHPExcel_Cell::stringFromColumnIndex($sheetMaxColumnIndex - 1) . '1')
			->getCell('B1')
			->setValue($objectsData['header']);
		$excelSheet->getStyle('B1')->applyFromArray($bigHeaderStyle);


		/**
		 * TITLE
		 */


		$excelSheet->getRowDimension('2')->setRowHeight(90);

		$excelSheet->mergeCells('A2:' . PHPExcel_Cell::stringFromColumnIndex($sheetMaxColumnIndex - 1) . '2')
			->getCell('A2')
			->setValue($objectsData['title']);
		$excelSheet->getStyle('A2')->applyFromArray($mediumHeaderStyle);


		/**
		 * TIME SPAN INFO
		 */


		$excelSheet->getCell('A4')->setValue('Период:');
		$excelSheet->mergeCells('B4:' . PHPExcel_Cell::stringFromColumnIndex($sheetMaxColumnIndex - 1) . '4')
			->getCell('B4')
			->setValue('С ' . $objectsData['time_start'] . ' по ' . $objectsData['time_end']);

		$excelSheet->getCell('A5')->setValue('Сформирован:');
		$excelSheet->mergeCells('B5:' . PHPExcel_Cell::stringFromColumnIndex($sheetMaxColumnIndex - 1) . '5')
			->getCell('B5')
			->setValue($objectsData['time_create']);

		$excelSheet->getCell('A6')->setValue('Объект:');
		$excelSheet->mergeCells('B6:' . PHPExcel_Cell::stringFromColumnIndex($sheetMaxColumnIndex - 1) . '6')
			->getCell('B6')
			->setValue($objectsData['object_name']);

		$timeSpanHeaderStyle = $headerStyle;
		$timeSpanHeaderStyle['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
		$excelSheet->getStyle('A4:A6')->applyFromArray($timeSpanHeaderStyle);

		$timeSpanHeaderTextStyle = $textStyle;
		$timeSpanHeaderTextStyle['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
		$excelSheet->getStyle('B4:B6')->applyFromArray($timeSpanHeaderTextStyle);


		/**
		 * SUMMARY INFO
		 */


		$servicesInfoFirstRowIndex = 8;
		// Two header rows + data rows
		$servicesInfoLastRowIndex = $servicesInfoFirstRowIndex + 2 + count($objectsData['summary']['tdata']);
		$thresholdLastColumnIndex = PHPExcel_Cell::stringFromColumnIndex($objectsData['summary']['count_thresholds']);
		$statisticTotalColumnIndex = PHPExcel_Cell::stringFromColumnIndex($objectsData['summary']['count_thresholds'] + 1);
		$statisticTotalWithErrorsColumnIndex = PHPExcel_Cell::stringFromColumnIndex($objectsData['summary']['count_thresholds'] + 2);
		$statisticTotalWithoutErrorsColumnIndex = PHPExcel_Cell::stringFromColumnIndex($objectsData['summary']['count_thresholds'] + 3);

		/*// Add pie chart
		$servicesChartLabel = array(
			new \PHPExcel_Chart_DataSeriesValues('String', $excelSheet->getTitle() . '!$' . $statisticTotalColumnIndex . '$' . $servicesInfoFirstRowIndex, null, 1)
		);

		$servicesChartCategory = array(
			new \PHPExcel_Chart_DataSeriesValues('String', $excelSheet->getTitle() . '!$' . $statisticTotalWithErrorsColumnIndex . '$' . ($servicesInfoFirstRowIndex + 1) . ':$' . $statisticTotalWithoutErrorsColumnIndex . '$' . ($servicesInfoFirstRowIndex + 1), null, 2)
		);

		$servicesChartValues = array(
			new \PHPExcel_Chart_DataSeriesValues('Number', $excelSheet->getTitle() . '!$' . $statisticTotalWithErrorsColumnIndex . '$' . ($servicesInfoLastRowIndex) . ':$' . $statisticTotalWithoutErrorsColumnIndex . '$' . ($servicesInfoLastRowIndex), null, 2)
		);

		$servicesChartSeries = new \PHPExcel_Chart_DataSeries(
			\PHPExcel_Chart_DataSeries::TYPE_PIECHART,
			NULL,
			range(0, count($servicesChartValues) - 1),
			$servicesChartLabel,
			$servicesChartCategory,
			$servicesChartValues
		);

		$servicesChartLayout = new \PHPExcel_Chart_Layout();
		$servicesChartLayout->setShowVal(true);
		$servicesChartLayout->setShowPercent(true);

		$servicesChartPlotArea = new \PHPExcel_Chart_PlotArea($servicesChartLayout, array($servicesChartSeries));

		$servicesChartLegend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);

		$servicesChartTitle = new \PHPExcel_Chart_Title('Состояние оказания услуг');

		$servicesChart = new \PHPExcel_Chart(
			'Состояние оказания услуг',
			$servicesChartTitle,
			$servicesChartLegend,
			$servicesChartPlotArea,
			true,
			0,
			null,
			null
		);

		$servicesChart->setTopLeftPosition('A8');
		$servicesChart->setBottomRightPosition($statisticTotalWithoutErrorsColumnIndex . '17');

		$excelSheet->addChart($servicesChart);*/

		// Add headers
		// Add service type header, its always goes first
		$excelSheet->mergeCells('A' . $servicesInfoFirstRowIndex . ':A' . ($servicesInfoFirstRowIndex + 1))
			->getCell('A' . $servicesInfoFirstRowIndex)
			->setValue($objectsData['summary']['thead'][0]);

		// Add rest headers
		if ($objectsData['summary']['count_thresholds'] > 0) {
			if ($objectsData['summary']['count_thresholds'] > 1) {
				$excelSheet->mergeCells('B' . $servicesInfoFirstRowIndex . ':' . $thresholdLastColumnIndex . $servicesInfoFirstRowIndex);
			}
			$excelSheet->getCell('B' . $servicesInfoFirstRowIndex)->setValue('Пороговые значения');
		}

		$excelSheet->mergeCells($statisticTotalColumnIndex . $servicesInfoFirstRowIndex . ':' . $statisticTotalWithoutErrorsColumnIndex . $servicesInfoFirstRowIndex)
			->getCell($statisticTotalColumnIndex . $servicesInfoFirstRowIndex)
			->setValue('Состояние оказания услуг');

		$excelSheet->fromArray(
			array_slice($objectsData['summary']['thead'], 1),
			NULL,
			'B' . ($servicesInfoFirstRowIndex + 1),
			true
		);

		$excelSheet->getStyle('A' . $servicesInfoFirstRowIndex . ':' . $statisticTotalWithoutErrorsColumnIndex . $servicesInfoFirstRowIndex)->applyFromArray($mediumHeaderStyle);
		$excelSheet->getStyle('B' . ($servicesInfoFirstRowIndex + 1) . ':' . $statisticTotalWithoutErrorsColumnIndex . ($servicesInfoFirstRowIndex + 1))->applyFromArray($headerStyle);

		// Add data
		$excelSheet->fromArray(
			$objectsData['summary']['tdata'],
			NULL,
			'A' . ($servicesInfoFirstRowIndex + 2),
			true
		);

		// Add summary
		if ($objectsData['summary']['count_thresholds'] > 0) {
			$excelSheet->mergeCells('A' . $servicesInfoLastRowIndex . ':' . $thresholdLastColumnIndex . $servicesInfoLastRowIndex);
		}
		$excelSheet->getCell('A' . $servicesInfoLastRowIndex)->setValue('Итого:');
		$excelSheet->getCell($statisticTotalColumnIndex . $servicesInfoLastRowIndex)->setValue($objectsData['summary']['sum_total_items']);
		$excelSheet->getCell($statisticTotalWithErrorsColumnIndex . $servicesInfoLastRowIndex)->setValue($objectsData['summary']['sum_items_with_errors']);
		$excelSheet->getCell($statisticTotalWithoutErrorsColumnIndex . $servicesInfoLastRowIndex)->setValue($objectsData['summary']['sum_items_without_errors']);

		$excelSheet->getStyle('A' . ($servicesInfoFirstRowIndex + 2) . ':A' . $servicesInfoLastRowIndex)->applyFromArray($headerStyle);
		$excelSheet->getStyle('B' . ($servicesInfoFirstRowIndex + 2) . ':' . $statisticTotalWithoutErrorsColumnIndex . $servicesInfoLastRowIndex)->applyFromArray($textStyle);
		$excelSheet->getStyle('A' . $servicesInfoFirstRowIndex . ':' . $statisticTotalWithoutErrorsColumnIndex . $servicesInfoLastRowIndex)->applyFromArray($allBordersStyle);

		// Add note
		$excelSheet->mergeCells('A' . ($servicesInfoLastRowIndex + 1) . ':' . $statisticTotalWithoutErrorsColumnIndex . ($servicesInfoLastRowIndex + 1))
			->getCell('A' . ($servicesInfoLastRowIndex + 1))
			->setValue('* Приведенные параметры качества измеряются при загрузке канала доступа к L2/L3 VPN или канала доступа в сеть «Интернет» не более 75% от установленной скорости потока данных');
		$excelSheet->getStyle('A' . ($servicesInfoLastRowIndex + 1))->applyFromArray($textStyle);


		/**
		 * DETAIL INFO
		 */


		// TODO: Rewrite this part, its ugly
		// Add headers
		$detailInfoFirstRowIndex = $servicesInfoLastRowIndex + 3;

		$excelSheet->mergeCells('A' . $detailInfoFirstRowIndex . ':' . PHPExcel_Cell::stringFromColumnIndex($detailColumnsCount - 1) . $detailInfoFirstRowIndex)
			->getCell('A' . $detailInfoFirstRowIndex)
			->setValue('Услуги с нарушениями SLA');

		if (empty($detailThresholdColumns)) {
			$detailInfoDataFirstRowIndex = $detailInfoFirstRowIndex + 2;
			$detailInfoLastRowIndex = $detailInfoFirstRowIndex + 1 + count($objectsData['services_with_errors']['tdata']);

			$excelSheet->fromArray(
				$objectsData['services_with_errors']['thead'],
				NULL,
				'A' . ($detailInfoFirstRowIndex + 1),
				true
			);
		} else {
			$detailInfoColumnIndex = 0;
			$detailInfoDataFirstRowIndex = $detailInfoFirstRowIndex + 3;
			$detailInfoLastRowIndex = $detailInfoFirstRowIndex + 2 + count($objectsData['services_with_errors']['tdata']);

			for ($columnIndex = 0; $columnIndex < count($objectsData['services_with_errors']['thead']); ++$columnIndex) {
				if (in_array($columnIndex, $detailThresholdColumns)) {
					$excelSheet->mergeCells(PHPExcel_Cell::stringFromColumnIndex($detailInfoColumnIndex) . ($detailInfoFirstRowIndex + 1) . ':' . PHPExcel_Cell::stringFromColumnIndex($detailInfoColumnIndex + 1) . ($detailInfoFirstRowIndex + 1))
						->getCell(PHPExcel_Cell::stringFromColumnIndex($detailInfoColumnIndex) . ($detailInfoFirstRowIndex + 1))
						->setValue($objectsData['services_with_errors']['thead'][$columnIndex]);
					$excelSheet->getRowDimension($detailInfoFirstRowIndex + 1)->setRowHeight(30);

					$excelSheet->getCell(PHPExcel_Cell::stringFromColumnIndex($detailInfoColumnIndex) . ($detailInfoFirstRowIndex + 2))->setValue('Значение');
					$excelSheet->getCell(PHPExcel_Cell::stringFromColumnIndex($detailInfoColumnIndex + 1) . ($detailInfoFirstRowIndex + 2))->setValue('Пороговое значение');
					$detailInfoColumnIndex += 2;
				} else {
					$excelSheet->mergeCells(PHPExcel_Cell::stringFromColumnIndex($detailInfoColumnIndex) . ($detailInfoFirstRowIndex + 1) . ':' . PHPExcel_Cell::stringFromColumnIndex($detailInfoColumnIndex) . ($detailInfoFirstRowIndex + 2))
						->getCell(PHPExcel_Cell::stringFromColumnIndex($detailInfoColumnIndex) . ($detailInfoFirstRowIndex + 1))
						->setValue($objectsData['services_with_errors']['thead'][$columnIndex]);
					++$detailInfoColumnIndex;
				}
			}
		}

		$excelSheet->getStyle('A' . $detailInfoFirstRowIndex)->applyFromArray($mediumHeaderStyle);
		$excelSheet->getStyle('A' . ($detailInfoFirstRowIndex + 1) . ':' . PHPExcel_Cell::stringFromColumnIndex($detailColumnsCount - 1) . ($detailInfoDataFirstRowIndex - 1))->applyFromArray($headerStyle);

		// Add data
		for ($rowIndex = 0; $rowIndex < count($objectsData['services_with_errors']['tdata']); ++$rowIndex) {
			$detailInfoColumnIndex = 0;
			for ($columnIndex = 0; $columnIndex < count($objectsData['services_with_errors']['tdata'][$rowIndex]); ++$columnIndex) {
				if (is_array($objectsData['services_with_errors']['tdata'][$rowIndex][$columnIndex])) {
					$excelSheet->getCell(PHPExcel_Cell::stringFromColumnIndex($detailInfoColumnIndex) . ($detailInfoDataFirstRowIndex + $rowIndex))->setValue($objectsData['services_with_errors']['tdata'][$rowIndex][$columnIndex][0]);
					$excelSheet->getCell(PHPExcel_Cell::stringFromColumnIndex($detailInfoColumnIndex + 1) . ($detailInfoDataFirstRowIndex + $rowIndex))->setValue($objectsData['services_with_errors']['tdata'][$rowIndex][$columnIndex][1]);
					$excelSheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($detailInfoColumnIndex) . ($detailInfoDataFirstRowIndex + $rowIndex) . ':' . PHPExcel_Cell::stringFromColumnIndex($detailInfoColumnIndex + 1) . ($detailInfoDataFirstRowIndex + $rowIndex))->applyFromArray($objectsData['services_with_errors']['tdata'][$rowIndex][$columnIndex][2] ? $redTextStyle : $textStyle);
					$detailInfoColumnIndex += 2;
				} else {
					$excelSheet->getCell(PHPExcel_Cell::stringFromColumnIndex($detailInfoColumnIndex) . ($detailInfoDataFirstRowIndex + $rowIndex))->setValue($objectsData['services_with_errors']['tdata'][$rowIndex][$columnIndex]);
					$excelSheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($detailInfoColumnIndex) . ($detailInfoDataFirstRowIndex + $rowIndex))->applyFromArray($textStyle);
					++$detailInfoColumnIndex;
				}
			}
		}

		$excelSheet->getStyle('A' . $detailInfoFirstRowIndex . ':' . PHPExcel_Cell::stringFromColumnIndex($detailColumnsCount - 1) . $detailInfoLastRowIndex)->applyFromArray($allBordersStyle);

		/**
		 * HEADERS AND OUTPUT
		 */


		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $this->getGeneratedDocumentFileName() . '.xlsx"');
		header('Cache-Control: max-age=0');

		$excelWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		//$excelWriter->setIncludeCharts(true);

		ob_clean();
		$excelWriter->save('php://output');
	}

	/**
	 * Generate detail report in XLS format
	 * @param $reportData
	 * @throws \PHPExcel_Exception
	 * @throws \PHPExcel_Reader_Exception
	 */
	public function generateDetailReportXLS($reportData)
	{
		/**
		 * Generate excel document
		 */
		PHPExcel_Settings::setLocale('ru_ru');

		$excel = new PHPExcel();

		// Set excel document properties
		$excel->getProperties()->setTitle($this->getGeneratedDocumentTitle());

		$excelSheet = $excel->getActiveSheet();

		// Set excel page properties
		$excelSheet->setPrintGridlines(true);
		$excelSheet->getPageSetup()
			->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
			->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
			->setFitToPage(true)
			->setFitToWidth(1)
			->setFitToHeight(0)
			->setHorizontalCentered(true);

		// Add excel page title
		$excelSheet->setTitle($this->getGeneratedDocumentTitle());

		// Calculate page dimensions
		$summaryThresholdsLastColumnIndex = count($reportData['report'][0]['summary']['thresholds']['thead']) - 1;
		$summaryThresholdsLastColumnStringIndex = PHPExcel_Cell::stringFromColumnIndex($summaryThresholdsLastColumnIndex);
		$summaryDisparityFirstColumnStringIndex = PHPExcel_Cell::stringFromColumnIndex($summaryThresholdsLastColumnIndex + 1);
		$summaryLastColumnIndex = $summaryThresholdsLastColumnIndex + count($reportData['report'][0]['summary']['disparity']['thead']);
		$summaryLastColumnStringIndex = PHPExcel_Cell::stringFromColumnIndex($summaryLastColumnIndex);

		$intervalsLastColumnIndex = count($reportData['report'][0]['intervals']['thead']) - 1;
		$intervalsLastColumnStringIndex = PHPExcel_Cell::stringFromColumnIndex($intervalsLastColumnIndex);

		$lastColumnIndex = $intervalsLastColumnIndex > $summaryLastColumnIndex ? $intervalsLastColumnIndex : $summaryLastColumnIndex;
		$lastColumnStringIndex = PHPExcel_Cell::stringFromColumnIndex($lastColumnIndex);

		$lastRowIndex = 1;

		for ($i = 0; $i <= $lastColumnIndex; ++$i) {
			$excelSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setWidth(20);
		}

		// TODO: Add generator method for style arrays
		// Styles
		$textStyle = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap' => true
			),
			'font' => array(
				'bold' => false,
				'name' => 'Times New Roman',
				'size' => 10
			)
		);
		$leftAlignedTextStyle = $textStyle;
		$leftAlignedTextStyle['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;

		$headerStyle = $textStyle;
		$headerStyle['font']['bold'] = true;
		$leftAlignedHeaderStyle = $headerStyle;
		$leftAlignedHeaderStyle['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
		$mediumHeaderStyle = $headerStyle;
		$mediumHeaderStyle['font']['size'] = 14;
		$bigHeaderStyle = $headerStyle;
		$bigHeaderStyle['font']['size'] = 18;

		$allBordersStyle = array(
			'borders' => array(
				'allborders' => array(
					'style' => \PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);


		/**
		 * HEADER
		 */


		if (isset($reportData['header'])) {
			$excelSheet->getRowDimension($lastRowIndex)->setRowHeight(60);

			// Header logo
			$headerLogo = new \PHPExcel_Worksheet_Drawing();
			$headerLogo->setName($reportData['header']);
			$headerLogo->setDescription($reportData['header']);
			$headerLogo->setPath('resources/images/logo_mo.png');
			$headerLogo->setCoordinates('A' . $lastRowIndex);
			$headerLogo->setWidthAndHeight(60, 80);
			$headerLogo->setWorksheet($excelSheet);

			// Header text
			$excelSheet->mergeCells('B' . $lastRowIndex . ':' . $lastColumnStringIndex . $lastRowIndex)
				->getCell('B' . $lastRowIndex)
				->setValue($reportData['header']);
			$excelSheet->getStyle('B' . $lastRowIndex)->applyFromArray($bigHeaderStyle);

			++$lastRowIndex;
		}


		/**
		 * TITLE
		 */


		if (isset($reportData['title'])) {
			$excelSheet->getRowDimension($lastRowIndex)->setRowHeight(90);

			$excelSheet->mergeCells('A' . $lastRowIndex . ':' . $lastColumnStringIndex . $lastRowIndex)
				->getCell('A' . $lastRowIndex)
				->setValue($reportData['title']);
			$excelSheet->getStyle('A' . $lastRowIndex)->applyFromArray($mediumHeaderStyle);

			$lastRowIndex += 2;
		}


		/**
		 * TIME SPAN INFO
		 */


		$excelSheet->getCell('A' . $lastRowIndex)->setValue('Период:');
		$excelSheet->mergeCells('B' . $lastRowIndex . ':' . $lastColumnStringIndex . $lastRowIndex);
		if (isset($reportData['time_start']) && isset($reportData['time_end'])) {
			$excelSheet->getCell('B' . $lastRowIndex)->setValue('С ' . $reportData['time_start'] . ' по ' . $reportData['time_end']);
		}
		++$lastRowIndex;

		$excelSheet->getCell('A' . $lastRowIndex)->setValue('Сформирован:');
		$excelSheet->mergeCells('B' . $lastRowIndex . ':' . $lastColumnStringIndex . $lastRowIndex);
		if (isset($reportData['time_create'])) {
			$excelSheet->getCell('B' . $lastRowIndex)
				->setValue($reportData['time_create']);
		}
		++$lastRowIndex;

		$excelSheet->getCell('A' . $lastRowIndex)->setValue('Объект:');
		$excelSheet->mergeCells('B' . $lastRowIndex . ':' . $lastColumnStringIndex . $lastRowIndex);
		if (isset($reportData['object_name'])) {
			$excelSheet->getCell('B' . $lastRowIndex)
				->setValue($reportData['object_name']);
		}

		$excelSheet->getStyle('A' . ($lastRowIndex - 2) . ':A' . $lastRowIndex)->applyFromArray($leftAlignedHeaderStyle);
		$excelSheet->getStyle('B' . ($lastRowIndex - 2) . ':B' . $lastRowIndex)->applyFromArray($leftAlignedTextStyle);

		$lastRowIndex += 2;


		/**
		 * DETAIL INFO
		 */


		for ($objectIndex = 0; $objectIndex < count($reportData['report']); ++$objectIndex) {
			/**
			 * OBJECT INFO
			 */
			$objectInfoFirstRowIndex = $lastRowIndex;

			$excelSheet->mergeCells('A' . $lastRowIndex . ':' . $summaryLastColumnStringIndex . $lastRowIndex)
				->getCell('A' . $lastRowIndex)
				->setValue('Информация об услуге');
			$excelSheet->getStyle('A' . $lastRowIndex)->applyFromArray($mediumHeaderStyle);
			++$lastRowIndex;

			foreach ($reportData['report'][$objectIndex]['service_information'] as $key => $value) {
				$excelSheet->getCell('A' . $lastRowIndex)->setValue($key);
				$excelSheet->mergeCells('B' . $lastRowIndex . ':' . $summaryLastColumnStringIndex . $lastRowIndex)
					->getCell('B' . $lastRowIndex)
					->setValue($value);
				++$lastRowIndex;
			}

			$excelSheet->getStyle('A' . ($objectInfoFirstRowIndex + 1) . ':A' . ($lastRowIndex - 1))->applyFromArray($leftAlignedHeaderStyle);
			$excelSheet->getStyle('B' . ($objectInfoFirstRowIndex + 1) . ':' . $summaryLastColumnStringIndex . ($lastRowIndex - 1))->applyFromArray($leftAlignedTextStyle);
			$excelSheet->getStyle('A' . $objectInfoFirstRowIndex . ':' . $summaryLastColumnStringIndex . ($lastRowIndex - 1))->applyFromArray($allBordersStyle);

			++$lastRowIndex;

			/**
			 * SUMMARY INFO
			 */
			$thresholdInfoFirstRowIndex = $lastRowIndex;

			// Summary info headers
			$excelSheet->mergeCells('A' . $lastRowIndex . ':' . $summaryLastColumnStringIndex . $lastRowIndex)
				->getCell('A' . $lastRowIndex)
				->setValue('Сводная информация о нарушениях SLA');
			$excelSheet->getStyle('A' . $lastRowIndex)->applyFromArray($mediumHeaderStyle);
			++$lastRowIndex;

			$excelSheet->mergeCells('A' . $lastRowIndex . ':' . $summaryThresholdsLastColumnStringIndex . $lastRowIndex)
				->getCell('A' . $lastRowIndex)
				->setValue('Пороговые значения');
			$excelSheet->mergeCells($summaryDisparityFirstColumnStringIndex . $lastRowIndex . ':' . $summaryLastColumnStringIndex . $lastRowIndex)
				->getCell($summaryDisparityFirstColumnStringIndex . $lastRowIndex)
				->setValue('Несоответствие SLA');
			++$lastRowIndex;

			// Threshold info data
			$excelSheet->fromArray(
				$reportData['report'][$objectIndex]['summary']['thresholds']['thead'],
				NULL,
				'A' . $lastRowIndex,
				true
			);
			$excelSheet->fromArray(
				$reportData['report'][$objectIndex]['summary']['disparity']['thead'],
				NULL,
				$summaryDisparityFirstColumnStringIndex . $lastRowIndex,
				true
			);

			$excelSheet->getStyle('A' . ($lastRowIndex - 1) . ':' . $summaryLastColumnStringIndex . $lastRowIndex)->applyFromArray($headerStyle);

			++$lastRowIndex;

			// Threshold info data
			$excelSheet->fromArray(
				$reportData['report'][$objectIndex]['summary']['thresholds']['tdata'],
				NULL,
				'A' . $lastRowIndex,
				true
			);
			$excelSheet->fromArray(
				$reportData['report'][$objectIndex]['summary']['disparity']['tdata'],
				NULL,
				$summaryDisparityFirstColumnStringIndex . $lastRowIndex,
				true
			);

			$thresholdRowsCount = count($reportData['report'][$objectIndex]['summary']['thresholds']['tdata']);
			$SLARowsCount = count($reportData['report'][$objectIndex]['summary']['disparity']['tdata']);
			$lastRowIndex += $thresholdRowsCount > $SLARowsCount ? $thresholdRowsCount : $SLARowsCount;

			// Threshold info note
			$excelSheet->mergeCells('A' . $lastRowIndex . ':' . $summaryLastColumnStringIndex . $lastRowIndex)
				->getCell('A' . $lastRowIndex)
				->setValue('* Приведенные параметры качества измеряются при загрузке канала доступа к L2/L3 VPN или канала доступа в сеть «Интернет» не более 75% от установленной скорости потока данных');
			$excelSheet->getRowDimension($lastRowIndex)->setRowHeight(30);

			$excelSheet->getStyle('A' . ($thresholdInfoFirstRowIndex + 3) . ':' . $summaryLastColumnStringIndex . $lastRowIndex)->applyFromArray($textStyle);
			$excelSheet->getStyle('A' . $thresholdInfoFirstRowIndex . ':' . $summaryLastColumnStringIndex . ($lastRowIndex - 1))->applyFromArray($allBordersStyle);

			$lastRowIndex += 2;

			/**
			 * INTERVALS INFO
			 */
			$intervalsInfoFirstRowIndex = $lastRowIndex;

			// Detail info headers
			$excelSheet->mergeCells('A' . $lastRowIndex . ':' . $intervalsLastColumnStringIndex . $lastRowIndex)
				->getCell('A' . $lastRowIndex)
				->setValue('Детализация по нарушениях SLA');
			$excelSheet->getStyle('A' . $lastRowIndex)->applyFromArray($mediumHeaderStyle);
			++$lastRowIndex;

			$excelSheet->fromArray(
				$reportData['report'][$objectIndex]['intervals']['thead'],
				NULL,
				'A' . $lastRowIndex,
				true
			);
			$excelSheet->getStyle('A' . $lastRowIndex . ':' . $intervalsLastColumnStringIndex . $lastRowIndex)->applyFromArray($headerStyle);
			++$lastRowIndex;

			// Detail info data
			$excelSheet->fromArray(
				$reportData['report'][$objectIndex]['intervals']['tdata'],
				NULL,
				'A' . $lastRowIndex,
				true
			);
			$lastRowIndex += count($reportData['report'][$objectIndex]['intervals']['tdata']);
			$excelSheet->getStyle('A' . ($intervalsInfoFirstRowIndex + 2) . ':' . $intervalsLastColumnStringIndex . ($lastRowIndex - 1))->applyFromArray($textStyle);
			$excelSheet->getStyle('A' . $intervalsInfoFirstRowIndex . ':' . $intervalsLastColumnStringIndex . ($lastRowIndex - 1))->applyFromArray($allBordersStyle);

			++$lastRowIndex;
		}

		/**
		 * HEADERS AND OUTPUT
		 */


		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $this->getGeneratedDocumentFileName() . '.xlsx"');
		header('Cache-Control: max-age=0');

		$excelWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');

		ob_clean();
		$excelWriter->save('php://output');
	}

	/**
	 * Generate default report in XLS format
	 * @param $reportData
	 * @throws \PHPExcel_Exception
	 * @throws \PHPExcel_Reader_Exception
	 */
	public function generateDefaultReportXLS($reportData)
	{
		/**
		 * Generate excel document
		 */
		PHPExcel_Settings::setLocale('ru_ru');

		$excel = new PHPExcel();

		// Set excel document properties
		$excel->getProperties()->setTitle($this->getGeneratedDocumentTitle());

		$excelSheet = $excel->getActiveSheet();

		// Set excel page properties
		$excelSheet->setPrintGridlines(true);
		$excelSheet->getPageSetup()
			->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT)
			->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
			->setFitToPage(true)
			->setFitToWidth(1)
			->setFitToHeight(0)
			->setHorizontalCentered(true);

		// Add excel page title
		$excelSheet->setTitle($this->getGeneratedDocumentTitle());

		// Generate excel page headers
		$lastColumnIndex = 0;
		$documentHeaders = array();

		$documentFieldsCount = count($reportData['fields']);
		for (; $lastColumnIndex < $documentFieldsCount; ++$lastColumnIndex) {
			$excelSheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($lastColumnIndex))->setWidth(20);

			// Generate excel page column headers
			$documentHeaders[] = $reportData['fields'][$lastColumnIndex]['field_name'];
		}

		$lastColumnStringIndex = PHPExcel_Cell::stringFromColumnIndex($lastColumnIndex - 1);
		$lastRowIndex = 1;

		// TODO: Add generator method for style arrays
		// Styles
		$textStyle = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'wrap' => true
			),
			'font' => array(
				'bold' => false,
				'name' => 'Times New Roman',
				'size' => 10
			)
		);

		$headerStyle = $textStyle;
		$headerStyle['font']['bold'] = true;
		$mediumHeaderStyle = $headerStyle;
		$mediumHeaderStyle['font']['size'] = 14;
		$bigHeaderStyle = $headerStyle;
		$bigHeaderStyle['font']['size'] = 18;

		$allBordersStyle = array(
			'borders' => array(
				'allborders' => array(
					'style' => \PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);


		/**
		 * HEADER
		 */


		if (isset($reportData['header'])) {
			$excelSheet->getRowDimension($lastRowIndex)->setRowHeight(60);

			// Header logo
			$headerLogo = new \PHPExcel_Worksheet_Drawing();
			$headerLogo->setName($reportData['header']);
			$headerLogo->setDescription($reportData['header']);
			$headerLogo->setPath('resources/images/logo_mo.png');
			$headerLogo->setCoordinates('A' . $lastRowIndex);
			$headerLogo->setWidthAndHeight(60, 80);
			$headerLogo->setWorksheet($excelSheet);

			// Header text
			$excelSheet->mergeCells('B' . $lastRowIndex . ':' . $lastColumnStringIndex . $lastRowIndex)
				->getCell('B' . $lastRowIndex)
				->setValue($reportData['header']);
			$excelSheet->getStyle('B' . $lastRowIndex)->applyFromArray($bigHeaderStyle);

			++$lastRowIndex;
		}


		/**
		 * TITLE
		 */


		if (isset($reportData['title'])) {
			$excelSheet->getRowDimension($lastRowIndex)->setRowHeight(90);

			$excelSheet->mergeCells('A' . $lastRowIndex . ':' . $lastColumnStringIndex . $lastRowIndex)
				->getCell('A' . $lastRowIndex)
				->setValue($reportData['title']);
			$excelSheet->getStyle('A' . $lastRowIndex)->applyFromArray($mediumHeaderStyle);

			$lastRowIndex += 2;
		}


		/**
		 * TIME SPAN INFO
		 */


		$excelSheet->getCell('A' . $lastRowIndex)->setValue('Период:');
		$excelSheet->mergeCells('B' . $lastRowIndex . ':' . $lastColumnStringIndex . $lastRowIndex);
		if (isset($reportData['time_start']) && isset($reportData['time_end'])) {
			$excelSheet->getCell('B' . $lastRowIndex)->setValue('С ' . $reportData['time_start'] . ' по ' . $reportData['time_end']);
		}
		++$lastRowIndex;

		$excelSheet->getCell('A' . $lastRowIndex)->setValue('Сформирован:');
		$excelSheet->mergeCells('B' . $lastRowIndex . ':' . $lastColumnStringIndex . $lastRowIndex);
		if (isset($reportData['time_create'])) {
			$excelSheet->getCell('B' . $lastRowIndex)
				->setValue($reportData['time_create']);
		}
		++$lastRowIndex;

		$excelSheet->getCell('A' . $lastRowIndex)->setValue('Объект:');
		$excelSheet->mergeCells('B' . $lastRowIndex . ':' . $lastColumnStringIndex . $lastRowIndex);
		if (isset($reportData['object_name'])) {
			$excelSheet->getCell('B' . $lastRowIndex)
				->setValue($reportData['object_name']);
		}

		$timeSpanHeaderStyle = $headerStyle;
		$timeSpanHeaderStyle['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
		$excelSheet->getStyle('A' . ($lastRowIndex - 2) . ':A' . $lastRowIndex)->applyFromArray($timeSpanHeaderStyle);

		$timeSpanHeaderTextStyle = $textStyle;
		$timeSpanHeaderTextStyle['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
		$excelSheet->getStyle('B' . ($lastRowIndex - 2) . ':B' . $lastRowIndex)->applyFromArray($timeSpanHeaderTextStyle);

		$lastRowIndex += 2;

		/**
		 * DATA
		 */

		// Add excel page column headers
		$excelSheet->fromArray(
			$documentHeaders,
			NULL,
			'A' . $lastRowIndex,
			true
		);
		$excelSheet->getStyle('A' . $lastRowIndex . ':' . $lastColumnStringIndex . $lastRowIndex)->applyFromArray($headerStyle);
		$excelSheet->getStyle('A' . $lastRowIndex . ':' . $lastColumnStringIndex . $lastRowIndex)->applyFromArray($allBordersStyle);
		++$lastRowIndex;

		// Generate excel page data
		$dataIndex = 0;
		$documentData = array();

		for (; $dataIndex < count($reportData['data']); ++$dataIndex) {
			$documentData[$dataIndex] = array();

			for ($column = 0; $column < $documentFieldsCount; ++$column) {
				if (isset($reportData['data'][$dataIndex]->{$reportData['fields'][$column]['field_id']})) {
					$documentData[$dataIndex][] = $reportData['data'][$dataIndex]->{$reportData['fields'][$column]['field_id']};
				} else {
					$documentData[$dataIndex][] = NULL;
				}
			}
		}

		// Add excel page data
		$excelSheet->fromArray(
			$documentData,
			NULL,
			'A' . $lastRowIndex,
			true
		);
		$excelSheet->getStyle('A' . $lastRowIndex . ':' . $lastColumnStringIndex . ($lastRowIndex + $dataIndex - 1))->applyFromArray($textStyle);
		$excelSheet->getStyle('A' . $lastRowIndex . ':' . $lastColumnStringIndex . ($lastRowIndex + $dataIndex - 1))->applyFromArray($allBordersStyle);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $this->getGeneratedDocumentFileName() . '.xlsx"');
		header('Cache-Control: max-age=0');

		$excelWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');

		ob_clean();
		$excelWriter->save('php://output');
	}

	/**
	 * Generate summary report in PDF format
	 * @param $objectsData
	 */
	public function generateSummaryReportPDF($objectsData)
	{
		$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$documentName = $this->getGeneratedDocumentTitle();

		// Set document information
		$pdf->SetCreator('Univef SLA');
		$pdf->SetAuthor('');
		$pdf->SetTitle($documentName);
		$pdf->SetSubject('');

		// Set default header data
		$pdf->setPrintHeader(false);

		// Set default footer data
		$pdf->setPrintFooter(false);

		// Set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// Set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// Set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// Add a page
		$pdf->AddPage();

		//generate header
		$pdf->SetFont('timesbd_wr', '', 18);

		$pdf->MultiCell(45 / 3 + 4, 60 / 3, '<img src="resources/images/logo_mo.png" width="45" height="60" border="0">', 0, 'L', 0, 0, '', '', true, null, true);
		$pdf->MultiCell(0, 60 / 3, $objectsData['header'], 0, 'L', false, 1, '', '', true, 0, false, true, 60 / 3, 'M');
		$pdf->Ln(4);
		$header = '<table border="0" cellpadding="3">'
			/*<tr>
				<td width="10%">
					<img src="resources/images/logo_mo.png" alt="mo" width="45" height="60" border="0" />
				</td>
				<td width="90%">
				' . $objectsData['header'] . '
				</td>
			</tr>*/
			. '<tr>
				<td align="center" colspan="2" style="font-size: 14px;">
					' . $objectsData['title'] . '
				</td>
			</tr>
		</table>';

		$pdf->writeHTML($header);
		$yForDrawPie = $pdf->getY();
		//generate subheader
		$pdf->SetFont('times_wr', '', 12);
		$subHeader = '
		<div>
			<table width="50%">
				<tr>
					<td style="font-family: \'timesbd_wr\';">Период:</td>
					<td>С ' . $objectsData['time_start'] . ' по ' . $objectsData['time_end'] . '</td>
				</tr>
				<tr>
					<td style="font-family: \'timesbd_wr\';">Сформирован:</td>
					<td>' . $objectsData['time_create'] . '</td>
				</tr>
				<tr>
					<td style="font-family: \'timesbd_wr\';">Объект:</td>
					<td>' . $objectsData['object_name'] . '</td>
				</tr>
			</table>
		</div>';

		$pdf->writeHTML($subHeader);

		$summary = $objectsData['summary'];

		$pieData = array(
			'without' => $summary['sum_items_without_errors'],
			'with' => $summary['sum_items_with_errors']);
		$this->drawPieChart($pdf, $pieData, $yForDrawPie);
		$pdf->writeHTML('<br />');

		//----

		$pdf->SetFont('times_wr', '', 10);
		$summaryTable = '
		<table border="1" cellpadding="2">
			<thead>';

		$sumLen = array_sum($summary['average_length']);
		$percentPerLen = $sumLen / 100;

		for ($i = 0; $i < count($summary['average_length']); $i++) {
			$summary['average_length'][$i] = ceil($summary['average_length'][$i] / $percentPerLen * 100);
		}

		if (array_sum($summary['average_length']) > 100) {
			while (array_sum($summary['average_length']) != 100) {
				$key = array_keys($summary['average_length'], max($summary['average_length']));
				$summary['average_length'][$key[0]]--;
			}
		}


		$w1 = array_sum(array_slice($summary['average_length'], 1, $summary['count_thresholds']));
		$w2 = array_sum(array_slice($summary['average_length'], 1 + $summary['count_thresholds'], count($summary['average_length']) - 1 - $summary['count_thresholds']));
		$summaryTable .= '<tr>
							<th width="' . $summary['average_length'][0] . '%"></th>
							<th style="font-family: \'timesbd_wr\';" align="center" width="' . $w1 . '%" colspan="' . ($summary['count_thresholds']) . '">Пороговые значения</th>
							<th style="font-family: \'timesbd_wr\';" align="center" width="' . $w2 . '%" colspan="' . (count($summary['thead']) - $summary['count_thresholds'] - 1) . '">Состояние оказания услуги</th>
						</tr>';


		$colors = $this->pieColors;
		$summaryTable .= '<tr>';
		foreach ($summary['thead'] as $key => $headName) {
			if ($key == count($summary['thead']) - 1) {
				$addColor = 'background-color:' . $colors[0] . ';';
			} else if ($key == count($summary['thead']) - 2) {
				$addColor = 'background-color:' . $colors[1] . ';';
			} else {
				$addColor = '';
			}
			$summaryTable .= '<th style="font-family: \'timesbd_wr\';' . $addColor . '" width="' . $summary['average_length'][$key] . '%" align="center">' . $headName . '</th>';
		}
		$summaryTable .= '</tr></thead>';

		$summaryTable .= '<tbody>';
		foreach ($summary['tdata'] as $data) {
			$summaryTable .= '<tr>';
			foreach ($data as $key => $value) {
				if ($key === 0) {
					$style = 'style="font-family: \'timesbd_wr\';"';
				} else {
					$style = '';
				}
				$summaryTable .= '<td ' . $style . ' align="center" width="' . $summary['average_length'][$key] . '%">';
				if(is_array($value)) {
					$summaryTable .= '<table><tr>';
					foreach($value as $itemValue) {
						$summaryTable .= '<td>' . $itemValue . '</td>';
					}
					$summaryTable .= '</tr></table>';

				} else {
					$summaryTable .= $value;
				}

				$summaryTable .= '</td>';
			}
			$summaryTable .= '</tr>';

		}
		$footnote = '* Приведенные параметры качества' .
			' измеряются при загрузке канала доступа к L2/L3 VPN' .
			' или канала доступа в сеть «Интернет»' .
			' не более 75% от установленной скорости потока данных';
		$summaryTable .= ' 	<tr>
								<td style="font-size: 8px;" colspan="' . ($summary['count_thresholds'] + 1) . '" align="left">' . $footnote . '</td>
								<td style="font-family: \'timesbd_wr\';" align="center">' . $summary['sum_total_items'] . '</td>
								<td style="font-family: \'timesbd_wr\';" align="center">' . $summary['sum_items_with_errors'] . '</td>
								<td style="font-family: \'timesbd_wr\';" align="center">' . $summary['sum_items_without_errors'] . '</td>
							</tr>';

		$summaryTable .= '</tbody>';

		$summaryTable .= '</table>';
		$pdf->writeHTML($summaryTable);

//------------
		//TODO: Выделить в отдельный метод
		$servicesWithErrors = $objectsData['services_with_errors'];

		$sumLen = array_sum($servicesWithErrors['average_length']);
		$percentPerLen = $sumLen / 100;

		for ($i = 0; $i < count($servicesWithErrors['average_length']); $i++) {
			$servicesWithErrors['average_length'][$i] = ceil($servicesWithErrors['average_length'][$i] / $percentPerLen * 100);
		}

		if (array_sum($servicesWithErrors['average_length']) > 100) {
			while (array_sum($servicesWithErrors['average_length']) != 100) {
				$key = array_keys($servicesWithErrors['average_length'], max($servicesWithErrors['average_length']));
				$servicesWithErrors['average_length'][$key[0]]--;
			}
		}


		$tableTitle = '<br /><div align="center" style="font-size: 14px;font-family: \'timesbd_wr\';">Услуги с нарушением SLA</div><br />';
		$pdf->writeHTML($tableTitle);

		$tableWithErrorsSla = '<table border="1" cellpadding="2">';
		$tableWithErrorsSla .= '<thead>';

		$tableWithErrorsSla .= '<tr>';
		foreach ($servicesWithErrors['thead'] as $key => $headName) {
			$tableWithErrorsSla .= '<th style="font-family: \'timesbd_wr\';" width="' . $servicesWithErrors['average_length'][$key] . '%" align="center">' . $headName . '</th>';
		}
		$tableWithErrorsSla .= '</tr>';

		$tableWithErrorsSla .= '</thead>';

		$tableWithErrorsSla .= '<tbody>';
		foreach ($servicesWithErrors['tdata'] as $data) {
			$tableWithErrorsSla .= '<tr>';
			foreach ($data as $key => $value) {
				$tableWithErrorsSla .= '<td  align="center" width="' . $servicesWithErrors['average_length'][$key] . '%">';
				$tdValue = '';
				if(is_array($value)) {

					$tdValue = '<table><tr>';
					foreach($value as $itemValue) {

						$backgroundStyle = '';
						if ($itemValue[2] === true) {
							$backgroundStyle = 'style="background-color:' . $colors[1] . '"';
						}
						$tdValue .= '<td ' . $backgroundStyle . '>';
						$tdValue .= '<div>' . $itemValue[0] . '</div><div>___</div><div>' . $itemValue[1] . '</div>';
						$tdValue .= '</td>';
					}
					$tdValue .= '</tr></table>';



				} else{

					$tdValue = $value;

				}




				$tableWithErrorsSla .= $tdValue;
				$tableWithErrorsSla .= '</td>';



			}
			$tableWithErrorsSla .= '</tr>';
		}

		$tableWithErrorsSla .= '</tbody>';
		$tableWithErrorsSla .= '</table>';

		$pdf->writeHTML($tableWithErrorsSla);


		// Clean output buffer, close and output PDF document
		ob_clean();
		$pdf->Output($this->getGeneratedDocumentFileName() . '.pdf', 'D');
	}

	/**
	 * Generate detail report in PDF format
	 * @param $objectsData
	 */
	public function generateDetailReportPDF($objectsData)
	{
		$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$documentName = $this->getGeneratedDocumentTitle();

		// Set document information
		$pdf->SetCreator('Univef SLA');
		$pdf->SetAuthor('');
		$pdf->SetTitle($documentName);
		$pdf->SetSubject('');

		// Set default header data
		$pdf->setPrintHeader(false);
		// Set default footer data
		$pdf->setPrintFooter(false);

		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP / 4, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		$pdf->AddPage();

		//generate header
		$pdf->SetFont('timesbd_wr', '', 18);

		$pdf->MultiCell(45 / 3 + 4, 60 / 3, '<img src="resources/images/logo_mo.png" width="45" height="60" border="0">', 0, 'L', 0, 0, '', '', true, null, true);
		$pdf->MultiCell(0, 60 / 3, $objectsData['header'], 0, 'L', false, 1, '', '', true, 0, false, true, 60 / 3, 'M');
		$pdf->Ln(4);
		$header = '<table border="0" cellpadding="3">'
			/*<tr>
				<td width="10%">
					<img src="resources/images/logo_mo.png" alt="mo" width="45" height="60" border="0" />
				</td>
				<td width="90%">
				' . $objectsData['header'] . '
				</td>
			</tr>*/
			. '<tr>
				<td align="center" colspan="2" style="font-size: 14px;">
					' . $objectsData['title'] . '
				</td>
			</tr>
		</table>';

		$pdf->writeHTML($header, false);
		$pdf->Ln(2);

		//generate subheader
		$pdf->SetFont('times_wr', '', 12);
		$subHeader =
			'<div>
			<table width="100%" border="0">
				<tr>
					<td width="25%" style="font-family: \'timesbd_wr\';">Период:</td>
					<td>С ' . $objectsData['time_start'] . ' по ' . $objectsData['time_end'] . '</td>
				</tr>
				<tr>
					<td style="font-family: \'timesbd_wr\';">Сформирован:</td>
					<td>' . $objectsData['time_create'] . '</td>
				</tr>
				<tr>
					<td style="font-family: \'timesbd_wr\';">Объект:</td>
					<td>' . $objectsData['object_name'] . '</td>
				</tr>
			</table>
		</div>';

		$pdf->writeHTML($subHeader);

		$report = $objectsData['report'];
		foreach ($report as $reportKey => $item) {

			$tableTitle = '<div align="center" style="font-size: 14px;font-family: \'timesbd_wr\';">Информация об услуге</div>';
			$pdf->writeHTML($tableTitle);

			$serviceInformationTable = '
			<table border="1" cellpadding="2">';

			foreach ($item['service_information'] as $fieldName => $fieldValue) {
				$serviceInformationTable .= '
					<tr>
						<td align="left" width="30%" style="font-family: \'timesbd_wr\';">' . $fieldName . '</td>
						<td align="left" width="70%">' . $fieldValue . '</td>
					</tr>';
			}

			$serviceInformationTable .= '</table>';
			$pdf->writeHTML($serviceInformationTable, false);
			$pdf->Ln(3);

			//generate table title
			$tableTitle = '<div align="center" style="font-size: 14px;font-family: \'timesbd_wr\';">Сводная информация о нарушениях SLA</div>';
			$pdf->writeHTML($tableTitle);

			$summary = $item['summary'];
			$thresholds = $summary['thresholds'];
			$disparity = $summary['disparity'];

			//generate table
			$pdf->SetFont('times_wr', '', 10);
			$slaSummaryTables = '<table>';
			$slaSummaryTables .= '<tr><td width="40%">';
			$slaSummaryTables .= '
			<table border="1" cellpadding="2">
				<thead>
					<tr>
						<th colspan="' . count($thresholds['thead']) . '" align="center" style="font-family: \'timesbd_wr\';">Пороговые значения</th>
					</tr>';
			$slaSummaryTables .= '<tr>';
			foreach ($thresholds['thead'] as $thrHeadName) {
				$slaSummaryTables .= '<th align="center" style="font-family: \'timesbd_wr\';">' . $thrHeadName . '</th>';
			}

			$slaSummaryTables .= '</tr></thead>';
			$align = 'center';
			foreach ($thresholds['tdata'] as $raw) {
				$slaSummaryTables .= '<tr>';
				foreach ($raw as $k => $value) {
					if ($k === 0) {
						$align = 'left';
					} else {
						$align = 'center';
					}
					$slaSummaryTables .= '<td align="' . $align . '">' . $value . '</td>';
				}
				$slaSummaryTables .= '</tr>';
			}

			$footnote = '* Приведенные параметры качества измеряются при загрузке каналадоступа к L2/L3 VPN или канала доступа в сеть «Интернет» не более 75% от установленной скорости потока данных';

			$slaSummaryTables .= '
			<tfoot>
				<tr>
					<td colspan="' . count($thresholds['thead']) . '" align="left" style="font-size: 8px;">' . $footnote . '</td>
				</tr>
			</tfoot>
			</table></td>';

			$slaSummaryTables .= '<td width="5%">&nbsp;</td>';

			$slaSummaryTables .= '<td width="55%">
			<table border="1" cellpadding="2"><thead>
				<tr>
					<td colspan="' . count($disparity['thead']) . '" align="center" style="font-family: \'timesbd_wr\';">Несоответствие SLA</td>
				</tr>';
			$slaSummaryTables .= '<tr>';
			foreach ($disparity['thead'] as $dispHeadName) {
				$slaSummaryTables .= '<th align="center" style="font-family: \'timesbd_wr\';">' . $dispHeadName . '</th>';
			}
			$slaSummaryTables .= '</tr></thead>';


			$align = 'center';
			foreach ($disparity['tdata'] as $raw) {
				$slaSummaryTables .= '<tr>';
				foreach ($raw as $k => $value) {
					if ($k === 0) {
						$align = 'left';
					} else {
						$align = 'center';
					}
					$slaSummaryTables .= '<td align="' . $align . '">' . $value . '</td>';
				}
				$slaSummaryTables .= '</tr>';
			}

			$slaSummaryTables .= '</table></td></tr></table>';
			$pdf->writeHTML($slaSummaryTables);

			$pdf->AddPage();

			//generate table title
			$tableTitle = '<div align="center" style="font-size: 14px;font-family: \'timesbd_wr\';">Детализация по нарушениям SLA</div><br />';
			$pdf->writeHTML($tableTitle);

			$intervals = $item['intervals'];

			//generate table
			$slaSummaryDetailingTable = '
			<table width="100%" border="1" cellpadding="3">
			<thead>
				<tr>';
			foreach ($intervals['thead'] as $value) {
				$slaSummaryDetailingTable .=
					'<th align="center" style="font-family: \'timesbd_wr\';">' . $value . '</th>';
			}

			$slaSummaryDetailingTable .= '</tr></thead>';

			foreach ($intervals['tdata'] as $str) {

				$tmp = $this->timestampToInterval($str[3]);
				$slaSummaryDetailingTable .= '
				<tr>
					<td align="center">' . $str[0] . '</td>
					<td align="center">' . $str[1] . '</td>
					<td align="center">' . $str[2] . '</td>
					<td align="center">' . $tmp . '</td>
					<td align="center">' . $str[4] . '</td>
					<td align="left">' . $str[5] . '</td>
				</tr>
			';
			}

			$slaSummaryDetailingTable .= '</table>';
			$pdf->writeHTML($slaSummaryDetailingTable);

			if ($reportKey != count($report) - 1) {
				$pdf->AddPage();
			}
		}

		// Clean output buffer, close and output PDF document
		ob_clean();
		$pdf->Output($this->getGeneratedDocumentFileName() . '.pdf', 'D');
	}

	/**
	 * Generate default report in PDF format
	 * @param $reportData
	 */
	public function generateDefaultReportPDF($reportData)
	{
		$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		$documentName = $this->getGeneratedDocumentTitle();

		// Set document information
		$pdf->SetCreator('Univef SLA');
		$pdf->SetAuthor('');
		$pdf->SetTitle($documentName);
		$pdf->SetSubject('');

		// Set default header data
		$pdf->setPrintHeader(false);
		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
		//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

		// Set default footer data
		$pdf->setPrintFooter(false);
		//$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
		//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// Set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// Set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// Set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// Add a page
		$pdf->AddPage();

		//generate header
		$pdf->SetFont('timesbd_wr', '', 18);

		if (isset($reportData['header'])) {
			$pdf->MultiCell(45 / 3 + 4, 60 / 3, '<img src="resources/images/logo_mo.png" width="45" height="60" border="0">', 0, 'L', 0, 0, '', '', true, null, true);
			$pdf->MultiCell(0, 60 / 3, $reportData['header'], 0, 'L', false, 1, '', '', true, 0, false, true, 60 / 3, 'M');
			$pdf->Ln(4);
		}

		if (isset($reportData['title'])) {
			$header = '
				<table border="0" cellpadding="3">'
				/*<tr>
					<td width="10%">
						<img src="resources/images/logo_mo.png" alt="mo" width="45" height="60" border="0" />
					</td>
					<td width="90%">
					' . $reportSettings['header'] . '
					</td>
				</tr>*/
				. '<tr>
					<td align="center" colspan="2" style="font-size: 14px;">
					' . $reportData['title'] . '
					</td>
					</tr>
				</table>';
			$pdf->writeHTML($header);
		}


		//generate subheader
		$pdf->SetFont('times_wr', '', 12);
		$subHeader = '
		<div>
			<table width="100%">
				<tr>
					<td width="25%" style="font-family: \'timesbd_wr\';">Период:</td>
					<td>С ' . $reportData['time_start'] . ' по ' . $reportData['time_end'] . '</td>
				</tr>
				<tr>
					<td style="font-family: \'timesbd_wr\';">Сформирован:</td>
					<td>' . $reportData['time_create'] . '</td>
				</tr>
				<tr>
					<td style="font-family: \'timesbd_wr\';">Объект:</td>
					<td>' . $reportData['object_name'] . '</td>
				</tr>
			</table>
		</div>';

		$pdf->writeHTML($subHeader);

		$pdf->SetFont('times_wr', '', 10);


		// Generate table
		$table = '
					<table align="center" border="1" cellpadding="3">
						<thead>
							<tr>
				';
		for ($column = 0; $column < count($reportData['fields']); ++$column) {
			$table .= '<th>' . $reportData['fields'][$column]['field_name'] . '</th>';
		}

		$table .= '
							</tr>
						</thead>
						<tbody>
				';

		for ($row = 0; $row < count($reportData['data']); ++$row) {
			$table .= '<tr nobr="true">';
			for ($column = 0; $column < count($reportData['fields']); ++$column) {
				if (isset($reportData['data'][$row]->{$reportData['fields'][$column]['field_id']})) {
					$table .= '<td>' . $reportData['data'][$row]->{$reportData['fields'][$column]['field_id']} . '</td>';
				} else {
					$table .= '<td></td>';
				}
			}
			$table .= '</tr>';
		}

		$table .= '
						</tbody>
					</table>
				';

		$pdf->writeHTML($table);

		// Clean output buffer, close and output PDF document
		ob_clean();
		$pdf->Output($this->getGeneratedDocumentFileName() . '.pdf', 'D');
	}

	/**
	 * Prepare data for generate summary report
	 * @param $reportData
	 * @return array
	 */
	private function prepareSummaryData($reportData)
	{


		tolog('rrrrrrrrrrr');
		tolog($reportData);
		tolog('rrrrrrrrrrr');

		$addressPathElements = ['region', 'city', 'street', 'house'];

		$metrics = json_decode($reportData->report_template->metric, true);
		$fields = json_decode($reportData->report_template->fields, true);
		$parameter = json_decode($reportData->report_template->parameter, true);
		$summary = $reportData->data->summary;
		$servicesWithErrors = $reportData->data->data;

		$result = array(
			'header' => $parameter['header'],
			'title' => $parameter['title'],
			'time_start' => date("d.m.Y H:i:s", $reportData->time_start),
			'time_end' => date("d.m.Y H:i:s", $reportData->time_end),
			'time_request' => date('d.m.Y H:i:s', $reportData->parameter_time->request),
			'time_create' => date('d.m.Y H:i'),
			'object_name' => $reportData->parameter->object_name
		);

		$thead = array('Вид услуги');

		$codes = array();
		$format = array();
		$needGetMetricValue = array();
		$theadFromMetric = array();
		foreach ($metrics as $keyMetric => $metric) {
			$format[$keyMetric] = array();
			$theadFromMetric[] = $metric['threshold_name'] !== null ? $metric['threshold_name'] : $metric['name'];
			$codes[] = $metric['code'];
			foreach($metric['items'] as $keyItem => $item) {
				if (property_exists($servicesWithErrors[0], $metric['code'])) {//нужно выбрать только те, что есть и в ответе и в метрике
					if (isset($item['comparison_value']) && $item['comparison_value'] !== null) {
						if(!isset($needGetMetricValue[$keyMetric])) {
							$needGetMetricValue[$keyMetric] = array();
						}
						$needGetMetricValue[$keyMetric][$keyItem] = $item['comparison_value'];
					} else {
						if(!isset($needGetMetricValue[$keyMetric])) {
							$needGetMetricValue[$keyMetric] = array();
						}
						$needGetMetricValue[$keyMetric][$keyItem] = false;


					}
					if ( isset($item['format_type']) && $item['format_type']) {
						$type = '';
						$formatString = '';

						if ($item['format_type'] === 1) {
							$type = 'str';
							$formatString = $item['format'];
						} else if ($item['format_type'] === 2) {
							$type = 'date';
							$formatString = $item['format'];
						} else if ($item['format_type'] === 3) {
							$type = 'interval';
							$formatString = 'HH:MM:SS';
						}

						$format[$keyMetric][$keyItem] = array(
							'type' => $type,
							'format_string' => $formatString
						);
					}
				}
			}
		}



		//для контроля необходимости проверять items  в метрике
		foreach($needGetMetricValue as $k => $m) {
			$flagNeeded = false;
			foreach($m as $i) {
				if($i !== false) {
					$flagNeeded = true;
					break;
				}
			}
			if($flagNeeded === false) {
				$needGetMetricValue[$k] = false;
			}
		}





		$thead = array_merge($thead, $theadFromMetric);
		$thead[] = 'Кол-во услуг';
		$thead[] = 'С нарушениями SLA';
		$thead[] = 'Без нарушений SLA';

		$avgLen = array();

		$theadLen = count($thead);
		for ($i = 0; $i < $theadLen; $i++) {
			$tmpArr = explode(' ', $thead[$i]);
			foreach ($tmpArr as &$value) {
				$value = mb_strlen($value, 'utf-8');
			}
			$avgLen[] = max($tmpArr);
		}


		$tdata = array();
		$sumTotalItems = 0;
		$sumItemsWithErrors = 0;
		$sumItemsWithoutErrors = 0;
		for ($i = 0; $i < count($summary); $i++) {
			$SummaryItem = $summary[$i];
			$tdata[$i] = array();
			$tdata[$i][] = $SummaryItem->service_name;
			foreach ($codes as $j => $code) {

				if (is_array($needGetMetricValue[$j])) {
					$thr = array();
					foreach($needGetMetricValue[$j] as $compVal) {
						if($compVal === false) {
							$thr[] = '-';
						} else {
							$tmpThr = $compVal;
							switch ($format[$j]['type']) {
								case 'date':
									$thr[] = date($format[$j]['format_string'], $tmpThr);
									break;
								case 'str':
									$thr[] = sprintf($format[$j]['format_string'], $tmpThr);
									break;
								case 'interval':
									$thr[] = $this->timestampToInterval($tmpThr);
									break;
								default:
									break;
							}
						}
					}
					$tdata[$i][] = $thr;

				} else {
					$tmpThresh = (array)($SummaryItem->thresholds);
					if (isset($tmpThresh[$code]) !== true) {
						$tdata[$i][] = '-';
					} else {
						$tmpThrs = (array)$tmpThresh[$code];
						$thr = array();
						foreach($tmpThrs['value'] as $keyVal => $tmpVal) {
							if($tmpVal == '') {
								$thr[$keyVal] = '-';
							} else {
								$thr[$keyVal] = $tmpVal;
								switch ($format[$j]['type']) {
									case 'date':
										$thr[$keyVal] = date($format[$j]['format_string'], $tmpVal);
										break;
									case 'str':
										$thr[$keyVal] = sprintf($format[$j]['format_string'], $tmpVal);
										break;
									case 'interval':
										$thr[$keyVal] = $this->timestampToInterval($tmpVal);
										break;
									default:
										break;
								}
							}
						}
						$tdata[$i][] = $thr;
					}
				}
			}
			$tdata[$i][] = $SummaryItem->total_items;
			$tdata[$i][] = $SummaryItem->items_with_errors;
			$tdata[$i][] = $SummaryItem->total_items - $SummaryItem->items_with_errors;

			$sumTotalItems += $SummaryItem->total_items;
			$sumItemsWithErrors += $SummaryItem->items_with_errors;
			$sumItemsWithoutErrors += $SummaryItem->total_items - $SummaryItem->items_with_errors;
			$tdataLen = count($tdata[$i]);
			for ($j = 0; $j < $tdataLen; $j++) {
				if(is_array($tdata[$i][$j])) {
					foreach($tdata[$i][$j] as $tdataArrayValue) {
						$tmpArr = explode(' ', $tdataArrayValue);
						foreach ($tmpArr as &$value) {
							$value = mb_strlen($value, 'utf-8');
						}
						$avgLen[$j] = max(
							$avgLen[$j],
							max($tmpArr)
						);
					}
				} else {
					$tmpArr = explode(' ', $tdata[$i][$j]);
					foreach ($tmpArr as &$value) {
						$value = mb_strlen($value, 'utf-8');
					}
					$avgLen[$j] = max(
						$avgLen[$j],
						max($tmpArr)
					);
				}
			}
		}



		$result['summary'] = array(
			'thead' => $thead,
			'tdata' => $tdata,
			'sum_total_items' => $sumTotalItems,
			'sum_items_with_errors' => $sumItemsWithErrors,
			'sum_items_without_errors' => $sumItemsWithoutErrors,
			'count_thresholds' => count($codes),
			'average_length' => $avgLen
		);

		//--------------------------------

		$thead = array('№ п/п');

		$headStructure = array();
		$addressStructure = array();
		foreach ($fields as $name => $items) {
			if (in_array($name, $addressPathElements)) {
				foreach ($items as $key => $item) {
					$addressStructure[] = $name . '.' . $key;
				}
				unset($fields[$name]);
			} else {
				foreach ($items as $key => $item) {
					$thead[] = $item;
					$headStructure[] = $name . '.' . $key;
				}
			}
		}

		$thead[] = 'Адрес оказания услуги';
		$headStructure[] = 'address';

		$tmpMetric = array();
		foreach ($metrics as $metricItem) {
			$tmpMetric[$metricItem['code']] = $metricItem;
		}



		$insertIndexArr = count($headStructure);
		$thead = array_merge($thead, $theadFromMetric);
		foreach ($codes as $key => $code) {
			foreach($tmpMetric[$code]['items'] as $keyMetric => $item ) {
				if (isset($item['comparison_operator'])) {
					if ( isset($item['comparison_value']) && $item['comparison_value'] !== null) {
						if(!isset($headStructure[$insertIndexArr])) {
							$headStructure[$insertIndexArr] = array();
						}
						$headStructure[$insertIndexArr][] = array(
							$code,
							'threshold_value.' . $code,
							array(//сравнивать со значением
								'comparison_operator' => $item['comparison_operator'],
								'comparison_value' => $item['comparison_value']),
							$format[$key][$keyMetric]
						);
					} else {
						if(!isset($headStructure[$insertIndexArr])) {
							$headStructure[$insertIndexArr] = array();
						}
						$headStructure[$insertIndexArr][] = array(//сравнивать с порогом
							$code,
							'threshold_value.' . $code,
							array(
								'comparison_operator' => $item['comparison_operator'],
								'comparison_value' => 'threshold'),
							$format[$key][$keyMetric]
						);
					}
				} else {
					$headStructure[$insertIndexArr][] = array($code, 'threshold_value.' . $code, false, $format[$key][$keyMetric]);//не сравнивать
				}
			}
			$insertIndexArr++;
		}

		/*
		 "addfields": [{
		"field":"deviation_duration",
		"threshold":"deviation_threshold",
		"name": "Длительность Отклонения от QoS, чч:мм:сс",
		"format_type": 3
	}],
		 */

		if(isset($parameter['addfields']) && is_array($parameter['addfields'])) {
			foreach ($parameter['addfields'] as $fieldItem) {
				if (isset($fieldItem['name'])) {
					$thead[] = $fieldItem['name'];
					if (empty($fieldItem['threshold'])) {
						$headStructure[] = $fieldItem['field'];
					} else {
						$headStructure[] = array(
							$fieldItem['field'],
							$fieldItem['threshold'],
							false,
							array(
								'type' => 'interval',
								'format_string' => 'HH:MM:SS'
							)
						);
					}
				}
			}
		}



		$avgLen = array();
		$theadLen = count($thead);
		for ($i = 0; $i < $theadLen; $i++) {
			$tmpArr = explode(' ', $thead[$i]);
			foreach ($tmpArr as &$value) {
				$value = mb_strlen($value, 'utf-8');
			}
			$avgLen[] = max($tmpArr);
		}

		$tdata = array();
		$servicesWithErrorsCount = count($servicesWithErrors);
		for ($i = 0; $i < $servicesWithErrorsCount; $i++) {
			$tdata[$i] = array();
			$tdata[$i][] = $i + 1;
			foreach ($headStructure as $headKey => $fieldsName) {
				if (is_array($fieldsName)) {

					$thresholds = array();
					foreach ($summary as $ind => $obj) {
						if ($obj->{'service_id'} === $servicesWithErrors[$i]->{'service.id'}) {
							$thresholds = (array)($obj->thresholds);
							break;
						}
					}


					foreach($fieldsName as $fieldNameIndex/*from code (0,1)*/ => $fieldNameValue/*array with cod1, thr, comp, format*/) {

						//$fieldNameValue[0] //code$1

						$first = $servicesWithErrors[$i]->{$fieldNameValue[0]};
						$first = $first[$fieldNameIndex];


						$first = $first == '' ? '-' : $first;

						$second = null;

						$tmpV = $thresholds[$fieldNameValue[0]]->value;
						$threshold = $tmpV[$fieldNameIndex];

						$threshold = $threshold == '' ? '-' : $threshold;


						$needPaint = false;

						if (is_array($fieldNameValue[2])) {
							if ($fieldNameValue[2]['comparison_value'] == 'threshold') {
								if ($threshold != '-') {
									$second = $threshold;
									$needPaint = true;
								} else {
									$needPaint = false;
								}
							} else {
								$second = $fieldNameValue[2]['comparison_value'];
								$threshold = $second;//специально для 1го порога
								$needPaint = true;
							}

						} else {
							$needPaint = false;
						}


						if ($needPaint === true) {
							$firstF = floatval($first);
							$secondF = floatval($second);
							switch ($fieldNameValue[2]['comparison_operator']) {
								case '>':
									if ($firstF > $secondF) {
										$needPaint = true;
									} else {
										$needPaint = false;
									}
									break;
								case '<':
									if ($firstF < $secondF) {
										$needPaint = true;
									} else {
										$needPaint = false;
									}
									break;
								case '<=':
									if ($firstF <= $secondF) {
										$needPaint = true;
									} else {
										$needPaint = false;
									}
									break;
								case '>=':
									if ($firstF >= $secondF) {
										$needPaint = true;
									} else {
										$needPaint = false;
									}
									break;
								case '=':
									if ($firstF == $secondF) {
										$needPaint = true;
									} else {
										$needPaint = false;
									}
									break;
								case '!=':
									if ($firstF != $secondF) {
										$needPaint = true;
									} else {
										$needPaint = false;
									}
									break;
							}
						}

						//apply format
						if(is_array($fieldNameValue[3])) {
							switch ($fieldNameValue[3]['type']) {
								case 'date':
									$first = date($fieldNameValue[3]['format_string'], $first);
									$threshold = date($fieldNameValue[3]['format_string'], $threshold);
									break;
								case 'str':
									$first = sprintf($fieldNameValue[3]['format_string'], $first);
									$threshold = sprintf($fieldNameValue[3]['format_string'], $threshold);
									break;
								case 'interval':
									if ($first != '-') {
										$first = $this->timestampToInterval($first);
									}
									if ($threshold != '-') {
										$threshold = $this->timestampToInterval($threshold);
									}
									break;
								default:
									break;

							}
						}




						if(!isset($tdata[$i][$headKey + 1])) {
							$tdata[$i][$headKey + 1] = array();
						}


						$tdata[$i][$headKey + 1][$fieldNameIndex] = array($first, $threshold, $needPaint);



					}


				} else if ($fieldsName == 'address') {
					$addressStructureCount = count($addressStructure);
					$arrStr = array();
					for ($j = 0; $j < $addressStructureCount; $j++) {
						if ($servicesWithErrors[$i]->{$addressStructure[$j]} != '') {
							$arrStr[] = $servicesWithErrors[$i]->{$addressStructure[$j]};
						}
					}
					$tdata[$i][$headKey + 1] = implode(', ', $arrStr);
				} else if ($fieldsName == 'deviation_duration') {// hardcode for qos
					$tmp = $servicesWithErrors[$i]->{$fieldsName} == '' ? '-' : ($servicesWithErrors[$i]->{$fieldsName});
					if ($tmp != '-') {
						$tmp = $this->timestampToInterval($tmp);
					}
					$tdata[$i][$headKey + 1] = $tmp;
				} else {
					$tdata[$i][$headKey + 1] = $servicesWithErrors[$i]->{$fieldsName} == '' ? '-' : $servicesWithErrors[$i]->{$fieldsName};
				}
			}


			/*
                        $tdataLen = count($tdata[$i]);
                        for ($j = 0; $j < $tdataLen; $j++) {

                            if (is_array($tdata[$i][$j])) {
                                $avgLen[$j] = max(
                                    $avgLen[$j],
                                    mb_strlen($tdata[$i][$j][0], 'utf-8'),
                                    mb_strlen($tdata[$i][$j][1], 'utf-8')
                                );
                            } else {
                                $tmpArr = explode(' ', $tdata[$i][$j]);
                                foreach ($tmpArr as &$value) {
                                    $value = mb_strlen($value, 'utf-8');
                                }
                                $avgLen[$j] = max(
                                    $avgLen[$j],
                                    max($tmpArr)
                                );
                            }
                        }
                        */
		}

		$result['services_with_errors'] = array(
			'thead' => $thead,
			'tdata' => $tdata,
			'average_length' => $avgLen
		);

		//--------------------------------
		return $result;
	}

	/**
	 * Prepare data to generate detail report
	 * @param $reportData
	 * @return array
	 */
	private function prepareDetailData($reportData)
	{
		$addressPathElements = ['region', 'city', 'street', 'house'];

		$metrics = json_decode($reportData->report_template->metric, true);
		$fields = json_decode($reportData->report_template->fields, true);
		$parameter = json_decode($reportData->report_template->parameter, true);
		$summary = $reportData->data->summary;
		$servicesWithErrors = $reportData->data->data;

		$result = array(
			'header' => $parameter['header'],
			'title' => $parameter['title'],
			'time_start' => date("d.m.Y H:i:s", $reportData->time_start),
			'time_end' => date("d.m.Y H:i:s", $reportData->time_end),
			'time_request' => date('d.m.Y H:i:s', $reportData->parameter->time_request),
			'time_create' => date('d.m.Y H:i'),
			'object_name' => $reportData->parameter->object_name,
			'report' => array()
		);

		//address structure
		$addressStructure = array();
		foreach ($addressPathElements as $pathName) {
			if (isset($fields[$pathName])) {
				foreach ($fields[$pathName] as $key => $item) {
					$addressStructure[] = $pathName . '.' . $key;
				}
			}
		}


		foreach ($servicesWithErrors as $key => $item) {
			//address
			$addressStructureCount = count($addressStructure);
			$arrStr = array();
			for ($j = 0; $j < $addressStructureCount; $j++) {
				if ($item->{$addressStructure[$j]} != '') {
					$arrStr[] = $item->{$addressStructure[$j]};
				}
			}
			$address = implode(', ', $arrStr);

			$result['report'][$key] = array(
				'service_information' => array(
					$fields['administration']['name'] => $item->{'administration.name'},
					$fields['office']['name'] => $item->{'office.name'},
					'Адрес оказания услуги' => $address,
					$fields['service']['name'] => $item->{'service.name'},
				)
			);

			//tables

			//summary
			$thresholds = array();
			foreach ($summary as $ind => $obj) {
				if ($obj->{'service_id'} === $item->{'service.id'}) {
					$thresholds = (array)($obj->thresholds);
					break;
				}
			}

			$variableHeadStructureT = array('Значение', 'Длительность');
			$thresholdsTable = array(
				'thead' => array('Характеристика'),//длительность
				'tdata' => array()
			);
			$variableHeadStructureD = array('Значение', 'Длительность');
			$disparityTable = array(
				'thead' => array('Характеристика'),
				'tdata' => array()
			);

			foreach ($thresholds as $thresholdName => $thresholdItem) {
				foreach ($metrics as $metric) {
					if ($metric['code'] == $thresholdName && $metric['hidden'] != 1) {

						$indexOfItems = array();//для понимания последовательности отображения
						$thrArrValues = $thresholdItem->value;
						$format = array();
						foreach ($metric['items'] as $metricItemKey => $metricItem) {
							$indexOfItems[$metricItemKey] = intval($metricItem['index']);

							if ($metricItem['format_type']) {
								$type = '';
								$formatString = '';

								if ($metricItem['format_type'] === 1) {
									$type = 'str';
									$formatString = $metricItem['format'];
								} else if ($metricItem['format_type'] === 2) {
									$type = 'date';
									$formatString = $metricItem['format'];
								} else if ($metricItem['format_type'] === 3) {
									$type = 'interval';
									$formatString = 'HH:MM:SS';
								}

								$format[$metricItemKey] = array(
									'type' => $type,
									'format_string' => $formatString
								);
							}

							if (isset($metricItem['use_db_threshold']) && $metricItem['use_db_threshold'] == 1) {
								if ($thrArrValues[$metricItemKey] == null) {
									$thrArrValues[$metricItemKey] = '-';
								}
							} else {
								if ($metricItem['comparison_value']) {
									$thrArrValues[$metricItemKey] = $metricItem['comparison_value'];

									switch ($format[$metricItemKey]['type']) {
										case 'date':
											$thrArrValues[$metricItemKey] = date($format[$metricItemKey]['format_string'], $thrArrValues[$metricItemKey]);
											break;
										case 'str':
											$thrArrValues[$metricItemKey] = sprintf($format[$metricItemKey]['format_string'], $thrArrValues[$metricItemKey]);
											break;
										case 'interval':
											$thrArrValues[$metricItemKey] = $this->timestampToInterval($thrArrValues[$metricItemKey]);
											break;
										default:
											break;
									}

								} else {
									$thrArrValues[$metricItemKey] = '-';
								}
							}
						}

						$dataMetric = $item->{$thresholdName};
						foreach ($dataMetric as $t => &$val) {
							if ($val == null) {
								$val = '-';
							} else {
								switch ($format[$t]['type']) {
									case 'date':
										$val = date($format[$t]['format_string'], $val);
										break;
									case 'str':
										$val = sprintf($format[$t]['format_string'], $val);
										break;
									case 'interval':
										$val = $this->timestampToInterval($val);
										break;
									default:
										break;
								}
							}
						}


						if (count($indexOfItems) > 0) {
							$tmp1 = array();
							$tmp2 = array();
							foreach ($indexOfItems as $k => $indexOfItem) {
								$tmp1[$indexOfItem] = $thrArrValues[$k];
								$tmp2[$indexOfItem] = $dataMetric[$k];
							}

							if (count($tmp1) < max(array_keys($tmp1))) {
								for ($i = 0; $i < max(array_keys($tmp1)); $i++) {
									if (empty($tmp1[$i])) {
										$tmp1[$i] = ' ';
									}
								}
							}

							ksort($tmp1);
							$thrArrValues = $tmp1;


							if (count($tmp2) < max(array_keys($tmp2))) {
								for ($i = 0; $i < max(array_keys($tmp2)); $i++) {
									if (empty($tmp2[$i])) {
										$tmp2[$i] = ' ';
									}
								}
							}

							ksort($tmp2);
							$dataMetric = $tmp2;

						}

						if (count($thrArrValues) > 1) { //hardcode for threshold fot duration
							while (count($thrArrValues) > 1) {
								array_pop($thrArrValues);
							}
						}
						$name = $metric['threshold_name'] !== null ? $metric['threshold_name'] : $metric['name'];
						array_unshift(
							$thrArrValues,
							$name
						);
						$thresholdsTable['tdata'][] = $thrArrValues;
						array_unshift(
							$dataMetric,
							$metric['name']

						);

						$disparityTable['tdata'][] = $dataMetric;
					}
				}
			}

			//вырваниваем число столбцов в заголовках и в данных


			$delta = count($thresholdsTable['tdata'][0]) - count($thresholdsTable['thead']);
			for ($i = 0; $i < $delta; $i++) {
				if (($headName = array_shift($variableHeadStructureT)) !== null) {
					$thresholdsTable['thead'][] = $headName;
				} else {
					$thresholdsTable['thead'][] = ' ';
				}

			}

			$delta = count($disparityTable['tdata'][0]) - count($disparityTable['thead']);
			for ($i = 0; $i < $delta; $i++) {
				if (($headName = array_shift($variableHeadStructureD)) !== null) {
					$disparityTable['thead'][] = $headName;
				} else {
					$disparityTable['thead'][] = ' ';
				}

			}


			$result['report'][$key]['summary'] = array(
				'thresholds' => $thresholdsTable,
				'disparity' => $disparityTable
			);

			//intervals
			$thead = array(
				'№ п/п',
				'Дата начала',
				'Дата окончания',
				'Длительность, чч:мм:cc',
				'Характеристика',
				'Причина'
			);

			$intervals = $item->intervals;
			$tdata = array();
			foreach ($intervals as $ind => $interval) {
				$tdata[] = array(
					$ind + 1,
					gmdate('d.m.Y H:i:s', $interval->{'time_start'}),
					gmdate('d.m.Y H:i:s', $interval->{'time_end'}),
					(intval($interval->{'time_end'}) - intval($interval->{'time_start'})),
					$interval->{'metric'},
					$interval->{'reason'}
				);

			}

			$result['report'][$key]['intervals'] = array(
				'thead' => $thead,
				'tdata' => $tdata,
			);

		}

		return $result;
	}

	/**
	 * Prepare data to generate default report
	 * @param $reportData
	 * @return array
	 */
	private function prepareDefaultData($reportData)
	{
		$parameter = json_decode($reportData->report_tempalte->parameter, true);

		$result = array(
			'header' => $parameter['header'],
			'title' => $parameter['title'],
			'time_start' => date("d.m.Y H:i:s", $reportData->time_start),
			'time_end' => date("d.m.Y H:i:s", $reportData->time_end),
			'time_request' => date('d.m.Y H:i:s', $reportData->parameter->time_request),
			'time_create' => date('d.m.Y H:i'),
			'object_name' => $reportData->parameter->object_name,
			'fields' => array(),
			'data' => $reportData->data
		);

		// Build single 'fields' mapping based on report template fields and metrics
		$fields = json_decode($reportData->report_template->fields);
		if (is_object($fields)) {
			foreach ($fields as $fieldId => $subFields) {
				foreach ($subFields as $subFieldId => $subFieldName) {
					$result['fields'][] = array(
						'field_id' => $fieldId . '.' . $subFieldId,
						'field_name' => (!empty($subFieldName)) ? $subFieldName : $fieldId . '.' . $subFieldId
					);
				}
			}
		}

		$metrics = json_decode($reportData->report_template->metric);
		if (is_array($metrics)) {
			foreach ($metrics as $metric) {
				if (isset($metric->code) && !empty($metric->code)) {
					$result['fields'][] = array(
						'field_id' => $metric->code,
						'field_name' => (isset($metric->name) && !empty($metric->name)) ? $metric->name : $metric->code
					);
				}
			}
		}

		return $result;
	}

	/**
	 * Convert timestamp to interval HH:MM:SS
	 * @param $timestamp
	 * @return string
	 */
	private function timestampToInterval($timestamp)
	{
		$timestamp = intval($timestamp);
		$h = intval($timestamp / (60 * 60));
		$m = intval(($timestamp - 60 * 60 * $h) / 60);
		$s = $timestamp - $h * 60 * 60 - $m * 60;
		if (strlen((string)$h) === 1) {
			$h = '0' . $h;
		}
		if (strlen((string)$m) === 1) {
			$m = '0' . $m;
		}
		if (strlen((string)$s) === 1) {
			$s = '0' . $s;
		}

		return $h . ':' . $m . ':' . $s;
	}

	/**
	 * Draw pie chart
	 * @param TCPDF $pdf
	 * @param $data
	 * @param $yForDraw
	 */
	private function drawPieChart(TCPDF $pdf, $data, $yForDraw)
	{

		$delta = 2; // for pie sector
		$aRect = 2;
		$innerMarginLegend = 40;
		$colors = $this->pieColors;

		$w = $pdf->getPageWidth();
		$partWidth = $w / 2;
		$r = $partWidth / 8;
		$xOfRightPie = $w - $r * 2 - $delta;
		$xOfRightLegend = $w - $partWidth + $innerMarginLegend - $delta;

		$sum = 0;

		foreach ($data as $value) {
			$sum += $value;
		}
		if ($sum !== 0) {
			$percent_without = round($data['without'] / $sum * 100, 1);

			$startAngle = 0;
			$endAngle = 0;
			$i = 0;
			$y = $yForDraw;

			$yLegend = $y;

			$anglQ = (360 * $data['without']) / $sum;

			$deltaX = (pow($delta, 2) - (pow($delta * tan(deg2rad($anglQ / 2)), 2)) + 2 * $xOfRightPie * $delta * cos(deg2rad($anglQ / 2)) + pow($delta * cos(deg2rad($anglQ / 2)), 2)) / (2 * $delta * cos(deg2rad($anglQ / 2)));
			$deltaY = $y - sqrt(pow($delta, 2) - pow(($deltaX - $xOfRightPie), 2));


			$endAngle = $startAngle + $anglQ;
			$rgb = $this->hex2rgb($colors[$i]);
			$pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
			if ($data['without'] !== 0) {
				if ($data['with'] === 0) {
					$pdf->Circle($deltaX, $deltaY + $r, $r, 0, 360, 'FD', array(), array($rgb[0], $rgb[1], $rgb[2]));
				} else {
					$pdf->PieSector($deltaX, $deltaY + $r, $r, $startAngle, $endAngle, 'FD', false, 0);
				}
			}
			$startAngle = $endAngle;

			$pdf->Rect($xOfRightLegend, $yLegend + $aRect * $i * 2, $aRect, $aRect, 'DF');
			$pdf->Text($xOfRightLegend + 2 * $aRect, $yLegend - $aRect, "Без нарушений " . $percent_without . "%");

			$i++;

			$anglUnq = (360 * $data['with']) / $sum;

			$endAngle = $startAngle + $anglUnq;
			$rgb = $this->hex2rgb($colors[$i]);
			$pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);
			if ($data['with'] !== 0) {
				if ($data['without'] === 0) {
					$pdf->Circle($xOfRightPie, $y + $r, $r, 0, 360, 'FD', array(), array($rgb[0], $rgb[1], $rgb[2]));
				} else {
					$pdf->PieSector($xOfRightPie, $y + $r, $r, $startAngle, $endAngle, 'FD', false, 0);
				}
			}
			$startAngle = $endAngle;

			$pdf->Rect($xOfRightLegend, $yLegend + $aRect * $i * 2, $aRect, $aRect, 'DF');
			$pdf->Text($xOfRightLegend + 2 * $aRect, $yLegend + $aRect, "С нарушениями " . round(100 - $percent_without, 1) . "%");

			$pdf->SetY($y + 2 * $r);
			//return $pdf->getY();
		}
	}

	/**
	 * Convert colors from hex to rgb
	 * @param $hex
	 * @return array($r, $g, $b)
	 */
	private function hex2rgb($hex)
	{
		$hex = str_replace("#", "", $hex);

		if (strlen($hex) == 3) {
			$r = hexdec($hex[0] . $hex[0]);
			$g = hexdec($hex[1] . $hex[1]);
			$b = hexdec($hex[2] . $hex[2]);
		} else {
			$r = hexdec($hex[0] . $hex[1]);
			$g = hexdec($hex[2] . $hex[3]);
			$b = hexdec($hex[4] . $hex[5]);
		}

		return array($r, $g, $b);
	}
}