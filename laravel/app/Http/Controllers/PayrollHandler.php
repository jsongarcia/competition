<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class PayrollHandler extends Controller
{
    public function getSalary(Request $r, $id){
        $emp = DB::select("select * from Employee where ID=?",[$id]);
        if(count($emp)==1){
            $hrPerDay=8;
            $hourly = $emp[0]->Hourly;

            $str=$r->getContent();
            $json = json_decode(html_entity_decode($str));
            
            $startDate = date_create_from_format('m/d/Y', $json->startDate);
            $endDate = date_create_from_format('m/d/Y', $json->endDate);
            $totalSalary=0;
            if(count(DB::select("select * from EmployeeLog Where EmployeeID=?",[$emp[0]->ID]))>0 ){
            if($startDate<=$endDate){
                $HolidayDates=[ // Format: d/m/Y
                    "01/01/2022",
                    "23/01/2022",
                    "01/02/2022",
                    "02/02/2022",
                    "15/02/2022",
                    "25/02/2022",
                    "09/04/2022",
                    "14/04/2022",
                    "15/04/2022",
                    "16/04/2022",
                    "17/04/2022",
                    "27/04/2022",
                    "01/05/2022",
                    "02/05/2022",
                    "12/06/2022",
                    "19/06/2022",
                    "09/07/2022",
                    "27/07/2022",
                    "30/07/2022",
                    "21/08/2022",
                    "29/08/2022",
                    "10/09/2022",
                    "08/10/2022",
                    "01/11/2022",
                    "02/11/2022",
                    "30/11/2022",
                    "08/12/2022",
                    "24/12/2022",
                    "25/12/2022",
                    "30/12/2022",
                    "31/12/2022"

                ];
                $currentDate=$startDate;
                //Salary = (normal hours + leave hours + overtime and holiday hours + holiday hours + overtime hours) x hourly rate
                do{
                    //Check first if the current date exists
                    if(count(DB::select("select * from EmployeeLog Where Date=? and EmployeeID=?", [$currentDate->format('Y/m/d'), $id]))>0){
                    //check if sat/sun = OVERTIME
                    if( in_array($currentDate->format("d/m/Y"), $HolidayDates) ){ //Check if holliday
                        $TimeIn = DB::select("select * from EmployeeLog Where timeType=? and Date=? and EmployeeID=?", ["in", $currentDate->format('Y/m/d'), $id])[0];
                        $TimeOut = DB::select("select * from EmployeeLog Where timeType=? and Date=? and EmployeeID=?", ["out", $currentDate->format('Y/m/d'), $id])[0];
                        $TotalHours = intval(date_diff(date_create_from_format("H:i:s", $TimeIn->Time),date_create_from_format("H:i:s", $TimeOut->Time))->format("%H"));
                        $days = 
                        $mult=2;
                        $typ=0;
                        if($TotalHours<=8){
                            $totalSalary+=($hourly*$mult)*$TotalHours;
                        }else{
                            $totalSalary+=($hourly*$mult)*8;
                            $TotalHours-=8;
                            //Holliday Overtime
                            $totalSalary+=($hourly*($mult*1.5))*$TotalHours;
                        }
                    }else if($currentDate->format("D")=="Sat" || $currentDate->format("D")=="Sun"){//Week ends
                        $TimeIn = DB::select("select * from EmployeeLog Where timeType=? and Date=? and EmployeeID=?", ["in", $currentDate->format('Y/m/d'), $id])[0];
                        $TimeOut = DB::select("select * from EmployeeLog Where timeType=? and Date=? and EmployeeID=?", ["out", $currentDate->format('Y/m/d'), $id])[0];
                        $TotalHours = intval(date_diff(date_create_from_format("H:i:s", $TimeIn->Time),date_create_from_format("H:i:s", $TimeOut->Time))->format("%H"));
                        $mult=1.5;
                        $typ=1;
                        if($TotalHours<=8){
                            $totalSalary+=($hourly*$mult)*$TotalHours;
                        }else{
                            $totalSalary+=($hourly*$mult)*8;
                            $TotalHours-=8;
                            //OVertime's Overtime
                            $totalSalary+=($hourly*($mult*2))*$TotalHours;
                        }
                    }else{//NOrmal Day
                        $TimeIn = DB::select("select * from EmployeeLog Where timeType=? and Date=? and EmployeeID=?", ["in", $currentDate->format('Y/m/d'), $id])[0];
                        $TimeOut = DB::select("select * from EmployeeLog Where timeType=? and Date=? and EmployeeID=?", ["out", $currentDate->format('Y/m/d'), $id])[0];
                        $TotalHours = intval(date_diff(date_create_from_format("H:i:s", $TimeIn->Time),date_create_from_format("H:i:s", $TimeOut->Time))->format("%H"));
                        $mult=1.5;
                        $typ=2;
                        if($TotalHours<=8){
                            $totalSalary+=($hourly)*$TotalHours;
                        }else{
                            $totalSalary+=($hourly*$mult)*8;
                            $TotalHours-=8;
                            //Holliday Overtime
                            $totalSalary+=($hourly*$mult)*$TotalHours;
                        }
                    }
                    
                }
                $currentDate->modify("+1 day");
            }while($currentDate<=$endDate);
            }

            if(count(DB::select("select * from EmployeeLog WHERE Date=2022-12-24 AND EmployeeID=?",[$id]))>0){//worked at dec 24, add 13th month
                $startDate = date_create_from_format('m/d/Y', "01/01/2022");
                $endDate = date_create_from_format('m/d/Y', "12/31/2022");
                $currentDate=$startDate;
                $TotalHours=0;
                if($startDate<=$endDate){
                    do{
                        if(count(DB::select("select * from EmployeeLog Where Date=? and EmployeeID=?", [$currentDate->format('Y/m/d'), $id]))>0){
                            $TimeIn = DB::select("select * from EmployeeLog Where timeType=? and Date=? and EmployeeID=?", ["in", $currentDate->format('Y/m/d'), $id])[0];
                            $TimeOut = DB::select("select * from EmployeeLog Where timeType=? and Date=? and EmployeeID=?", ["out", $currentDate->format('Y/m/d'), $id])[0];
                            $TotalHours += intval(date_diff(date_create_from_format("H:i:s", $TimeIn->Time),date_create_from_format("H:i:s", $TimeOut->Time))->format("%H"));
                        }
                    }while($currentDate<=$endDate);
                    //TOtal Hours got
                    //Get hourly
                    $totalSalary+=$hourly*$TotalHours;
                }
            }
        }

        
        return $totalSalary;
        }else{
                return response(404);
        }
    }

    public function timeIn(Request $r, $id){
        if(count(DB::select("select * from Employee where ID=?",[$id]))==1){
            $str=$r->getContent();
            $json = json_decode(html_entity_decode($str));
            
            $startDate = date_create_from_format('m/d/Y H:i', $json->timeInDate);
            DB::insert("insert into EmployeeLog(EmployeeID, timeType,Date, Time) VALUES(?,?,?,?)",[
                $id, "in", $startDate->format('Y/m/d'), $startDate->format('H:i')
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
            
            $startDate = date_create_from_format('m/d/Y H:i', $json->timeOutDate);
            DB::insert("insert into EmployeeLog(EmployeeID, timeType,Date, Time) VALUES(?,?,?,?)",[
                $id, "out", $startDate->format('Y/m/d'), $startDate->format('H:i')
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
                    DB::insert("insert into EmployeeLog(EmployeeID, timeType, Date, Time) values(?,?,?,?)",[
                        $id, 'in', $Date, '08:00:00'
                    ]);
                    DB::insert("insert into EmployeeLog(EmployeeID, timeType, Date, Time) values(?,?,?,?)",[
                        $id, 'out', $Date, '16:00:00'
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
                    DB::insert("insert into EmployeeLog(EmployeeID, timeType, Date, Time) values(?,?,?,?)",[
                        $id, 'in', $Date, '08:00:00'
                    ]);
                    DB::insert("insert into EmployeeLog(EmployeeID, timeType, Date, Time) values(?,?,?,?)",[
                        $id, 'out', $Date, '16:00:00'
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
