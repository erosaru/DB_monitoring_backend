<?php

namespace App\Http\Controllers;
use DB;

class sqlMonitoringController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function get_trouble_query(){
        $trouble_queries = DB::table('tbl_history_trouble_processlist as a')
            ->where("a.is_kill", "=", 0)
            ->get();
        // $db = app('db');
        // $accountsDatabase = $db->connection('accounts');
        // $threeNewestUsers = app('db')->select("SELECT * FROM g_users ORDER BY created_at DESC LIMIT 3");
        // DB::select("SELECT * FROM users");
        if($trouble_queries->count() > 0)
            return response()->json(['code_result' => 0, 'message_result' => 'Data ', 'data' => $trouble_queries->toArray()]);
        else
            return response()->json(['code_result' => 1, 'message_result' => 'Tidak ada data']);
    }

    public function cron_check_live_server($db = null){
        $connection = env('DB_SETTING', 'local');
        $max_time = env('MAX_TIME', 750);
        // Test database connection
        try {
            DB::connection($connection)->getPdo();
        } catch (\Exception $e) {
            die($e->getMessage()." at ".date("Y-m-d H:i:s")."\n");
        }

        //if connection build then check processlist
        $processlist = DB::connection($connection)
            ->table('information_schema.processlist as a')
            ->where("a.TIME", ">", $max_time)
            ->whereNotNull("a.INFO")
            ->get();
        $query_trouble_count = $processlist->count();
        foreach($processlist as $row){
            // $row->ID = 55;
            // $row->HOST = "localhost:60602";
            // $row->TIME = 1000;
            $list = DB::connection("local")
                ->table('tbl_history_trouble_processlist as a')
                ->where("a.host", "=", $row->HOST)
                ->where("a.process_id", "=", $row->ID)
                ->where("a.user", "=", $row->USER)
                ->get();

            if($list->count() > 0){
                $data = array(
                    'time' => $row->TIME,
                );
                DB::connection("local")
                    ->table('tbl_history_trouble_processlist as a')
                    ->where("a.host", "=", $row->HOST)
                    ->where("a.process_id", "=", $row->ID)
                    ->where("a.user", "=", $row->USER)
                    ->update($data);
            }else{
                $data = array(
                    'host' => $row->HOST,
                    'process_id' => $row->ID,
                    'query' => $row->INFO,
                    'user' => $row->USER,
                    'time' => $row->TIME
                );
                DB::connection("local")->table('tbl_history_trouble_processlist')->insert($data);
            }
        }
        if($query_trouble_count > 0){
            echo "There are $query_trouble_count query must kill at ".date("Y-m-d H:i:s");
        }
    }

    function sendGCM($message, $id) {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array (
            'registration_ids' => array (
                $id
                ),
            'data' => array (
                "message" => $message
                )
            );
        $fields = json_encode ( $fields );
        $headers = array (
            'Authorization: key=' . "YOUR_KEY_HERE",
            'Content-Type: application/json'
            );
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );
        $result = curl_exec ( $ch );
        echo $result;
        curl_close ( $ch );
    }


}
