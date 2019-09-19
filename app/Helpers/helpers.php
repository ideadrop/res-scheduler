
<?php

/**
 * Generate an versioned asset path for the application.
 *
 * @param  string $path
 * @param  bool $secure
 * @return string
 */
function asset_versioned($path, $secure = null)
{
    $version = env('APP_VERSION', time());
    $versionPath = $path . '?' . $version;
    return app('url')->asset($versionPath, $secure);
}
function formatAllocationDate($timestamp){
    return \Carbon\Carbon::parse($timestamp)->toDateString();
}
function getAllocatablePercentage($data){

    $start = $data['start_date'];
    $end = $data['end_date'];
    $resourceId = $data['resource_id'];


    $allocationId = (isset($data['exclude_allocation_id']))?$data['exclude_allocation_id']:'';

    $maxAllocation = 0;

    $dates = new DatePeriod(
        new DateTime($start),
        DateInterval::createFromDateString('1 day'),
        new DateTime($end)
    );

    foreach ( $dates as $date ):
        $checkDate = "'".$date->format('Y-m-d')."'";

        $query = DB::table('allocations as al')
            ->select('allocation_value')
            ->where('assignee_id', '=', $resourceId)
            ->whereRaw("DATE(al.start_date) <= $checkDate")
            ->whereRaw("DATE(al.end_date) > $checkDate");

        if($allocationId!=''){
            $query->where('al.id', '!=', $allocationId);
        }

        $consumedAllocation = $query->sum('allocation_value');

        $consumedAllocation = (is_numeric($consumedAllocation))?$consumedAllocation:0;
        $maxAllocation = ($maxAllocation>$consumedAllocation)?$maxAllocation:$consumedAllocation;
    endforeach;

    return (100-$maxAllocation);
}

function getColors($count = false){

    $list = [
        '#FF3333','#33E3FF','#FF8333','#33FFA8','#33FFFF','#3371FF','#D733FF','#33FFE0','#FF3399','#33B2FF','#FF3352','#E8646E','#7D33FF','#827A7A','#66C0D5','#5C6296','#CAA9EC','#8AAC8E','#ACAA8A','#7F7E79'
    ];

    if(!$count){$count = count($list);}

    return  array_slice($list, 0, $count);

}
/**
 * Method to convert array to comma separated string.
 * @param $content
 * @return mixed
 * @author Rameez Rami <ramees.pu@cubettech.com>
 * @since 2017-08-02
 */
function arrayToComma($content)
{
    if ($content == '') {
        return '';
    }
    $string = rtrim(implode(',', $content), ',');
    return $string;
}

/**
 * Method to convert array to comma separated string.
 * @param $content
 * @return mixed
 * @author Rameez Rami <ramees.pu@cubettech.com>
 * @since 2017-08-02
 */
function commaToArray($content)
{
    if ($content == '') {
        return [];
    }
    $arr = explode(',', $content);
    return $arr;
}
function appName(){

    return env('APP_NAME','ResourceScheduler');
}
function appUrl(){

    return env('APP_URL','ResourceScheduler');
}