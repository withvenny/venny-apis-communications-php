<?php

    //
    header('Content-Type: application/json');

    //
    use Communications\Connection as Connection;
    use Communications\Token as Token;
    use Communications\Notification as Notification;

    // connect to the PostgreSQL database
    $pdo = Connection::get()->connect();

    // STEP 1. Receive passed variables / information
    if(isset($_REQUEST['app'])){$request['app'] = clean($_REQUEST['app']);}
    if(isset($_REQUEST['domain'])){$request['domain'] = clean($_REQUEST['domain']);}
    if(isset($_REQUEST['token'])){$request['token'] = clean($_REQUEST['token']);}

    // INITIATE DATA CLEANSE
    if(isset($_REQUEST['id'])){$request['id'] = clean($_REQUEST['id']);}
    if(isset($_REQUEST['attributes'])){$request['attributes'] = clean($_REQUEST['attributes']);}
    if(isset($_REQUEST['message'])){$request['message'] = clean($_REQUEST['message']);}
    if(isset($_REQUEST['type'])){$request['type'] = clean($_REQUEST['type']);}
    if(isset($_REQUEST['opened'])){$request['opened'] = clean($_REQUEST['opened']);}
    if(isset($_REQUEST['viewed'])){$request['viewed'] = clean($_REQUEST['viewed']);}
    if(isset($_REQUEST['recipient'])){$request['recipient'] = clean($_REQUEST['recipient']);}
    if(isset($_REQUEST['sender'])){$request['sender'] = clean($_REQUEST['sender']);}
    if(isset($_REQUEST['subject'])){$request['subject'] = clean($_REQUEST['subject']);}
    if(isset($_REQUEST['object'])){$request['object'] = clean($_REQUEST['object']);}
    if(isset($_REQUEST['profile'])){$request['profile'] = clean($_REQUEST['profile']);}
    
    //
    switch ($_SERVER['REQUEST_METHOD']) {

        //
        case 'POST':

            try {

                // 
                $notification = new Notification($pdo);
            
                // insert a stock into the stocks table
                $id = $notification->insertNotification($request);

                $request['id'] = $id;

                $results = $notification->selectNotifications($request);

                $results = json_encode($results);
                
                //
                echo $results;
            
            } catch (\PDOException $e) {

                echo $e->getMessage();

            }

        break;

        //
        case 'GET':

            //
            if(isset($_REQUEST['per'])){$request['per'] = clean($_REQUEST['per']);}
            if(isset($_REQUEST['page'])){$request['page'] = clean($_REQUEST['page']);}
            if(isset($_REQUEST['limit'])){$request['limit'] = clean($_REQUEST['limit']);}        

            try {

                // 
                $notification = new Notification($pdo);

                // get all stocks data
                $results = $notification->selectNotifications($request);

                $results = json_encode($results);

                echo $results;

            } catch (\PDOException $e) {

                echo $e->getMessage();

            }

        break;

        //
        case 'PUT':

            try {

                // 
                $notification = new Notification($pdo);
            
                // insert a stock into the stocks table
                $id = $notification->updateNotification($request);

                $request['id'] = $id;

                $results = $notification->selectNotifications($request);

                $results = json_encode($results);

                echo $results;
            
            } catch (\PDOException $e) {

                echo $e->getMessage();

            }

        break;

        //
        case 'DELETE':

            try {

                // 
                $notification = new Notification($pdo);
            
                // insert a stock into the stocks table
                $id = $notification->deleteNotification($request);

                echo 'The record ' . $id . ' has been deleted';
            
            } catch (\PDOException $e) {

                echo $e->getMessage();

            }

        break;

    }

?>
