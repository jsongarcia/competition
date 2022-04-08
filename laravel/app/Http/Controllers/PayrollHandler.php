<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class PayrollHandler extends Controller
{
    public function getSalary(Request $r, $id){
        $emp = DB::select("select * from Employee where ID=?",[$id])[0];
        $hrPerDay=8;
        $hourly = $emp->Hourly;

        $str=$r->getContent();
        $json = json_decode(html_entity_decode($str));
        
        $startDate = date_create_from_format('m/d/Y', $json->startDate);
        $endDate = date_create_from_format('m/d/Y', $json->endDate);

        //Check if invalid
        if($startDate>$endDate){
            return 0;
        }else{
            return 1;
        }
    }

    public function timeIn(Request $r, $id){
        if(count(DB::select("select * from Employee where ID=?",[$id]))==1){
            $str=$r->getContent();
            $json = json_decode(html_entity_decode($str));
            
            $startDate = date_create_from_format('m/d/Y H:i', $json->timeInDate);
            DB::insert("insert into EmployeeLog(EmployeeID, timeType,Date, Time) VALUES(?,?,?,?)",[
                $id, "timeInDate", $startDate->format('Y/m/d'), $startDate->format('H:i')
            ]);
            return response(200);
        }else{
            return response(404);
        }
    }

    public function timeOut(Request $r, $id){
        if(count(DB::select("select * from Employee where ID=?",[$id]))==1){
            $str=$r->getContent();
            $json = json_decode(html_entity_decode($str));
            
            $startDate = date_create_from_format('m/d/Y H:i', $json->timeInDate);
            DB::insert("insert into EmployeeLog(EmployeeID, timeType,Date, Time) VALUES(?,?,?,?)",[
                $id, "timeOutDate", $startDate->format('Y/m/d'), $startDate->format('H:i')
            ]);
            return response(200);
        }else{
            return response(404);
        }
    }

    public function leave(Request $r, $id){
        $emp = DB::select("select * from Employee where ID=?",[$id]);
        if(count($emp)==1){
            $str=$r->getContent();
            $json = json_decode(html_entity_decode($str));
            
            if($json->type=="sick"){
                
                if($emp[0]->Sick>0){
                    $Date = date_create_from_format('m/d/Y', $json->markDate);
                    DB::insert("insert into EmployeeMarks(EmployeeID, markType, Date) values(?,?,?)",[
                        $id, $json->type,  $Date
                    ]);
                    DB::update("update Employee Set Sick=? WHERE ID=?",[
                        intval($emp[0]->Sick)-1, $id
                    ]);
                }else{
                    $Date = date_create_from_format('m/d/Y', $json->markDate);
                    DB::insert("insert into EmployeeMarks(EmployeeID, markType, Date) values(?,?,?)",[
                        $id, "absent",  $Date
                    ]);
                }
                return response(200);
            } else if($json->type=="leave"){
                
                if($emp[0]->Vacation>0){
                    $Date = date_create_from_format('m/d/Y', $json->markDate);
                    DB::insert("insert into EmployeeMarks(EmployeeID, markType, Date) values(?,?,?)",[
                        $id, $json->type,  $Date
                    ]);
                    DB::update("update Employee Set Vacation=? WHERE ID=?",[
                        intval($emp[0]->Vacation)-1, $id
                    ]);
                }else{
                    $Date = date_create_from_format('m/d/Y', $json->markDate);
                    DB::insert("insert into EmployeeMarks(EmployeeID, markType, Date) values(?,?,?)",[
                        $id, "absent",  $Date
                    ]);
                }

                return response(200);
            }
        }else{
            return response(404);
        }
        
    }
}
