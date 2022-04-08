<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
class EmployeeController extends Controller
{
    public function getEmp(){
        $emp = DB::select('select * from Employee');
            $res = [
                "employee"=>[
                    
                ]
            ];
            foreach($emp as $rec){
                $info = [
                    'id'=> $rec->ID,
                    'firstName'=> $rec->FName,
                    'lastName'=> $rec->LName,
                    'position'=> $rec->Position,
                    'sickLeaveCredits'=> $rec->Sick,
                    'vacationLeaveCredits'=>$rec->Vacation,
                    'hourlyRate'=> $rec->Hourly
                ];
                array_push($res["employee"], $info);
            }
            return response($res, 200);
    }


    public function getEmpById($id){
        $emp = DB::select('select * from Employee WHERE ID=?', [$id]);
        if(count($emp)==1){
            $res = [
                "employee"=>[
                    'id'=> $emp[0]->ID,
                    'firstName'=> $emp[0]->FName,
                    'lastName'=> $emp[0]->LName,
                    'position'=> $emp[0]->Position,
                    'sickLeaveCredits'=> $emp[0]->Sick,
                    'vacationLeaveCredits'=>$emp[0]->Vacation,
                    'hourlyRate'=> $emp[0]->Hourly
                ]
            ];
            return response($res, 200);
        }else{ //means no res
            return response(404);
        }
    }

    public function delEmpById($id){
        $empFound = DB::select('select * from Employee WHERE ID=?', [$id]);
        DB::delete('delete from Employee WHERE ID=?', [$id]);
        if(count($empFound)==1){
            return response(200);
        }else{
            return response(404);
        }
    }

    public function putEmpById(Request $r, $id){
        $str=$r->getContent();
        $json = json_decode(html_entity_decode($str));
        $emp = DB::select("select * from Employee WHERE ID=?",[$id]);
        if(count($emp)==1){
            DB::update("
            UPDATE Employee
            SET FName=?,LName=?,Position=?,Sick=?,Vacation=?,Hourly=?
            WHERE ID=?
            ",[
                $json->firstName,
                $json->lastName,
                $json->position,
                $json->sickLeaveCredits,
                $json->vacationLeaveCredits,
                $json->hourlyRate,
                $id
            ]);

            return response(200);

        }else{
            return response(404);
        }
    }

    public function saveEmp(Request $r){
        $str=$r->getContent();
        $json = json_decode(html_entity_decode($str));
        $foundEmployee = DB::select("select * from Employee where FName=? and LName=? and Position=? and Sick=? and Vacation=? and Hourly=?",[
            $json->firstName,
            $json->lastName,
            $json->position,
            $json->sickLeaveCredits,
            $json->vacationLeaveCredits,
            $json->hourlyRate,
        ]);

        $emp = [
            "employee"=>[]
        ];

        if(count($foundEmployee)==0){

            DB::insert("
            INSERT INTO Employee(FName, LName, Position, Sick, Vacation, Hourly)
            VALUES(?,?,?,?,?,?)
            ",[
                $json->firstName,
                $json->lastName,
                $json->position,
                $json->sickLeaveCredits,
                $json->vacationLeaveCredits,
                $json->hourlyRate,
            ]);
            $newEmp = DB::select("select * from Employee where FName=? and LName=? and Position=? and Sick=? and Vacation=? and Hourly=?",[
                $json->firstName,
                $json->lastName,
                $json->position,
                $json->sickLeaveCredits,
                $json->vacationLeaveCredits,
                $json->hourlyRate,
            ]);

            foreach($newEmp as $record){
                $info=[
                    "id"=>$record->ID,
                    "firstName"=>$record->FName,
                    "lastName"=>$record->Position,
                    "position"=>$record->Position,
                    "sickLeaveCredits"=>$record->Sick,
                    "vacationLeaveCredits"=>$record->Vacation,
                    "hourlyRate"=>$record->Hourly
                ];
                array_push($emp["employee"],$info);
            }
            return response($emp, 200);
        }else{
            return response(309);
        }
    }
}
