<?php

/*******************************************************************************
** sptGetChartData
** Gets the link data for the chart and returns an array ready for json encoding
** @param from_date - starting date to collect link data for
** @param to_date - ending date to collect link data for
** @param link_slug - limit the search to this link slug
** @param cat_slug - limit the search to this category slug
** @return array - organised link data
** @since 1.1
*******************************************************************************/
function sptGetChartData($splitTestID, $fromDate = null, $toDate = null) {
	$dataArray = array();

	// First, sanitize the inputs
	if ($fromDate == null || empty($fromDate))
		$fromDate = get_the_time('Y-m-d', $splitTestID);

	if ($toDate == null || empty($toDate))
		$toDate = date('Y-m-d', current_time('timestamp'));

	// For each link, find the number of clicks for each given date
	$dates = sptGetDays($fromDate, $toDate);

	// Get the test data
	$statsData = sptGetStatsData($splitTestID);
	$sptData = unserialize(get_post_meta($splitTestID, 'sptData', true));

	// Loop through the dates and count up the views
	foreach ($dates as $date) {
		$viewsForDate = 0;

		$unixStartOfDate = mktime(00, 00, 00, date("m", strtotime($date)), date("d", strtotime($date)), date("Y", strtotime($date)));
		$unixEndOfDate = mktime(23, 59, 59, date("m", strtotime($date)), date("d", strtotime($date)), date("Y", strtotime($date)));

		// Get this day's views on this page
		$masterViewsForDate = count(sptViewsForPeriod($statsData, $sptData['master_id'], $unixStartOfDate, $unixEndOfDate));
		$variationViewsForDate = count(sptViewsForPeriod($statsData, $sptData['slave_id'], $unixStartOfDate, $unixEndOfDate));

		// Add this day's views to the array
		$dataArray[] = array($date, (!empty($masterViewsForDate) ? $masterViewsForDate : 0), (!empty($variationViewsForDate) ? $variationViewsForDate : 0));
	}

	// Allow filtering of the chart data before return
	$dataArray = apply_filters('spt_chart_data', $dataArray, $splitTestID);

	return $dataArray;
}

/*******************************************************************************
** sptGetDays
** Gets the link data for the chart and returns an array ready for json encoding
** Credit must goto Ed Rackham for this code, adapted for use here
** (http://edrackham.com/php/get-days-between-two-dates-using-php/)
** @param sStartDate - starting date
** @param sEndDate - ending date
** @return array - dates in an array
** @since 1.1
*******************************************************************************/
function sptGetDays($sStartDate, $sEndDate){
	// Firstly, format the provided dates.
	// This function works best with YYYY-MM-DD
	// but other date formats will work thanks
	// to strtotime().

	$sStartDate = gmdate("Y-m-d", strtotime($sStartDate));
	$sEndDate = gmdate("Y-m-d", strtotime($sEndDate));

	// Start the variable off with the start date
	$aDays[] = $sStartDate;

	// Set a 'temp' variable, sCurrentDate, with
	// the start date - before beginning the loop
	$sCurrentDate = $sStartDate;

	// While the current date is less than the end date
	while ($sCurrentDate < $sEndDate) {
		// Add a day to the current date
		$sCurrentDate = gmdate("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));

		// Add this new day to the aDays array
		$aDays[] = $sCurrentDate;
	}

	// Once the loop has finished, return the
	// array of days.
	return $aDays;
}

/*******************************************************************************
** sptViewsForPeriod
** Return all the views within the given period as an array
** @since 1.1
*******************************************************************************/
function sptViewsForPeriod($statsData, $pageID, $startDate, $endDate) {

	$viewsForPeriod = array();

	if (!empty($statsData) && !empty($statsData[$pageID]['views'])) {
		foreach ($statsData[$pageID]['views'] as $view) {
			if ($view['timestamp'] > $startDate &&
				$view['timestamp'] <= $endDate) {

				// View was within date parameters
				$viewsForPeriod[] = $view;
			}
		}
	}

	usort($viewsForPeriod, 'sptStatsSortFunction');
	return $viewsForPeriod;
}

/*******************************************************************************
** sptStatsSortFunction
** Return all the clicks so far today as an array
** @since 1.1
*******************************************************************************/
function sptStatsSortFunction($dateA, $dateB) {
	if ($dateA['timestamp'] == $dateB['timestamp'])
		return 0;
	else
		return ($dateA['timestamp'] < $dateB['timestamp']) ? -1 : 1;
}

/*******************************************************************************
** sptResetAllStats
** Ajax handler to reset all stats for a test
** @since 1.2
*******************************************************************************/
function sptResetAllStats() {
	$sptID = sptFilterData($_POST['spt_id']);

	if (!is_numeric($sptID) && current_user_can('manage_options'))
		die();

	$sptData = unserialize(get_post_meta($sptID, 'sptData', true));

	if (empty($sptData))
		die();

	// Reset view stats
	$sptData[$sptData['master_id'] . '_visits'] = array();
	$sptData[$sptData['slave_id'] . '_visits'] = array();

	// Apply filters to allow add-ons to do the same
	$sptData = apply_filters('spt_after_stats_reset', $sptData);

	// Re-save meta values
	update_post_meta($sptID, 'sptData', serialize($sptData));

	// TODO: Reset view stats on archived stats too

	die();
}

/*******************************************************************************
** sptAjaxGetChartData
** Ajax wrapper function for sptGetChartData
** @since 1.1
*******************************************************************************/
function sptAjaxGetChartData() {

	if (!current_user_can('manage_options'))
		die();

	$splitTestID = (!empty($_POST['splitTestID']) ? $_POST['splitTestID'] : '');
	//$pageID = (!empty($_POST['pageID']) ? $_POST['pageID'] : '');
	$fromDate = (!empty($_POST['fromDate']) ? $_POST['fromDate'] : '');
	$toDate = (!empty($_POST['toDate']) ? $_POST['toDate'] : '');

	echo json_encode(sptGetChartData($splitTestID, $fromDate, $toDate));

	die();
}

/*******************************************************************************
** sptGetUniqueVisits
** Get the unique visitors of a specific view count.
**
** This function will return an array containing the unique visitors for the
** given views.
**
** The array will look like this when printed in JSON format:
**
** {
**     "10.0.2.1": { "count": 1 },
**     "10.0.2.2": { "count": 3 },
**     "10.0.2.3": { "count": 5 },
**     ...
** }
**
** The "count" value represents the number of times its associated IP have
** visited a page.
**
** @param $views array of SPT stats views
** @return array the unique views
** @since 1.3.4
*******************************************************************************/
function sptGetUniqueVisits( $views ) {
	$unique_views = array();

	if ( isset( $views ) && !empty( $views ) ) {
		foreach ( $views as $visit ) {
			$ip = $visit[ 'ip' ];

			if ( !isset( $slave[ $ip ] ) ) {
				$unique_views[ $ip ] = array(
					'count' => 0
				);
			}

			// Keep track of the number of visits.
			$unique_views[ $ip ][ 'count' ] += 1;
		}
	}

	return $unique_views;
}

/*******************************************************************************
** sptGetStatsData
** Retrieve all the stats data for the given test ID and return it.
** @param $splitTestID
** @return array formatted array of statistics
** @since 1.4.4
*******************************************************************************/
function sptGetStatsData( $splitTestID ) {

	if ( !is_numeric( $splitTestID ) || get_post_status( $splitTestID ) === FALSE )
		return array();

	// Get both the unarchived stats data from the test object, and also the
	// archived stats data (which is all data from the current day back)
	// then merge them and return.

	// Prepare variables
	$masterViews = array();
	$slaveViews = array();
	$masterUniqueViews = array();
	$slaveUniqueViews = array();
	$sptData = unserialize( get_post_meta( $splitTestID, 'sptData', true ) );
	$masterID = $sptData[ 'master_id' ];
	$slaveID = $sptData[ 'slave_id' ];

	// Get the unarchived stats data first
	$sptCurrentStats = array(
		$masterID => array(
			'views' => isset( $sptData[ $masterID . '_visits' ] ) ? $sptData[ $masterID . '_visits' ] : array()
		),
		$slaveID => array(
			'views' => isset( $sptData[ $slaveID . '_visits' ] ) ? $sptData[ $slaveID . '_visits' ] : array()
		)
	);

	// Get the archived stats data
	$sptStatsArchive = get_post_meta( $splitTestID, 'sptStatsArchive', true );

	if ( !isset( $sptStatsArchive ) || empty( $sptStatsArchive ) ) {
		$sptStatsArchive = array(
			$masterID => array(
				'views' => array()
			),
			$slaveID => array(
				'views' => array()
			)
		);
	}

	// Merge the non-archived and archived stats data together
	$masterViews = array_merge( $sptCurrentStats[ $masterID ][ 'views' ], $sptStatsArchive[ $masterID ][ 'views' ] );
	$slaveViews = array_merge( $sptCurrentStats[ $slaveID ][ 'views' ], $sptStatsArchive[ $slaveID ][ 'views' ] );

	// Return formatted array

	return array(

		$masterID => array(
			'views' => $masterViews,
			'unique_views' => sptGetUniqueVisits( $masterViews )
		),

		$slaveID => array(
			'views' => $slaveViews,
			'unique_views' => sptGetUniqueVisits( $slaveViews )
		)

	);
}

/*******************************************************************************
** sptArchiveStatisticsForTest
** Archive the split test statistics data into secondary meta data to allow for
** performance on split test pages during test run.
**
** @hooked sptArchiveStatisticsForTest
** @param $splitTestID
** @return array formatted array of statistics
** @since 1.4.4
*******************************************************************************/
function sptArchiveStatisticsForTest( $splitTestID ) {

	if ( !isset( $splitTestID ) || !is_numeric( $splitTestID ) )
		return;

	// Get the current stats recorded
	$sptData = unserialize( get_post_meta( $splitTestID, 'sptData', true ) );
	$masterID = $sptData[ 'master_id' ];
	$slaveID = $sptData[ 'slave_id' ];

	// Save to the Archive meta
	$sptStatsArchive = array();
	$sptStatsArchive = get_post_meta( $splitTestID, 'sptStatsArchive', true );

	$masterViews = array_merge(
		isset( $sptStatsArchive[ $masterID ][ 'views' ] ) ? $sptStatsArchive[ $masterID ][ 'views' ] : array(),
		isset( $sptData[ $masterID . '_visits' ] ) ? $sptData[ $masterID . '_visits' ] : array()
	);

	$slaveViews = array_merge(
		isset( $sptStatsArchive[ $slaveID ][ 'views' ] ) ? $sptStatsArchive[ $slaveID ][ 'views' ] : array(),
		isset( $sptData[ $slaveID . '_visits' ] ) ? $sptData[ $slaveID . '_visits' ] : array()
	);

	$sptStatsArchive = array(
		$masterID => array(
			'views' => $masterViews
		),
		$slaveID => array(
			'views' => $slaveViews
		)
	);

	update_post_meta( $splitTestID, 'sptStatsArchive', $sptStatsArchive );

	// Clear the current stats recorded
	$sptData[ $masterID . '_visits' ] = array();
	$sptData[ $slaveID . '_visits' ] = array();

	update_post_meta( $splitTestID, 'sptData', serialize( $sptData ) );
}

/*******************************************************************************
** sptUnscheduleStatsArchiveCron
** Unschedule the archiver cronjob
**
** @param $splitTestID
** @return array formatted array of statistics
** @since 1.4.4
*******************************************************************************/
function sptUnscheduleStatsArchiveCron( $splitTestID ) {
	$timestamp = wp_next_scheduled( 'spt_archive_stats', array( $splitTestID ) );
	wp_unschedule_event( $timestamp, 'spt_archive_stats', array( $splitTestID ) );
}

/*******************************************************************************
** sptArchiveDailyStats
** Manually archive the split test statistics
**
** @param $splitTestID
** @since 1.4.4
*******************************************************************************/
function sptArchiveDailyStats() {

	$splitTestID = sptFilterData( $_POST[ 'spt_id' ] );

	if (!is_numeric( $splitTestID ) && current_user_can( 'manage_options' ) )
		die();

	sptArchiveStatisticsForTest( $splitTestID );

}

/*******************************************************************************
** Register cron job actions
*******************************************************************************/
add_action( 'spt_archive_stats', 'sptArchiveStatisticsForTest', 10, 1);

/*******************************************************************************
** Register ajax calls
*******************************************************************************/
add_action('wp_ajax_sptAjaxGetChartData', 'sptAjaxGetChartData');
add_action('wp_ajax_sptResetAllStats', 'sptResetAllStats');
add_action('wp_ajax_sptArchiveDailyStats', 'sptArchiveDailyStats');
