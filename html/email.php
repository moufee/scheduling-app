<?php
if(getenv('SEND_MAIL')) {
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
    require_once('Mandrill.php');
    $respond_url = "http://grace-scheduling-testing.herokuapp.com/respond.php";
    define('RESPOND_URL', 'http://grace-scheduling-testing.herokuapp.com/respond.php');
    function sendSchedulingRequest($to, $name, $currentPosition, $currentWeekend, $neededPosition, $neededWeekend, $resolutionID, $personID, $expirationDate)
    {
        try {
            $mandrill = new Mandrill('1B54QfE4NOp7pkt0a7XCrA');
            $template_name = 'scheduling-request-version-2';
            $template_content = array(
                array(
                    'name' => 'example name',
                    'content' => 'example content'
                )
            );
            $message = array(
                'subject' => 'Scheduling Request',
                'from_email' => 'productionScheduling@dev.floret.us',
                'from_name' => 'Production Scheduling',
                'to' => array(
                    array(
                        'email' => $to,
                        //'name' => 'Recipient Name',
                        'type' => 'to'
                    )
                ),
                'headers' => array('Reply-To' => 'ben.ferris1@gmail.com'),
                'important' => false,
                'track_opens' => null,
                'track_clicks' => false,
                'auto_text' => null,
                'auto_html' => null,
                'inline_css' => null,
                'url_strip_qs' => null,
                'preserve_recipients' => null,
                'view_content_link' => null,
                //'bcc_address' => 'message.bcc_address@example.com',
                'tracking_domain' => null,
                'signing_domain' => null,
                'return_path_domain' => null,
                'merge' => true,
                'global_merge_vars' => array(
                    array()
                ),
                'merge_vars' => array(
                    array(
                        'rcpt' => $to,
                        'vars' => array(
                            array(
                                'name' => 'name',
                                'content' => $name
                            ),
                            array(
                                'name' => 'scheduledposition',
                                'content' => $currentPosition
                            ),
                            array(
                                'name' => 'scheduledweekend',
                                'content' => $currentWeekend
                            ),
                            array(
                                'name' => 'neededposition',
                                'content' => $neededPosition
                            ),
                            array(
                                'name' => 'neededweekend',
                                'content' => $neededWeekend
                            ),
                            array(
                                'name' => 'yeslink',
                                'content' => RESPOND_URL . '?response=yes&responderID=' . $personID . '&resolutionID=' . $resolutionID
                            ),
                            array(
                                'name' => 'nolink',
                                'content' => RESPOND_URL . '?response=no&responderID=' . $personID . '&resolutionID=' . $resolutionID
                            ),
                            array(
                                'name' => 'expirationdate',
                                'content' => $expirationDate
                            )
                        )
                    )
                ),
            );
            $result = $mandrill->messages->sendTemplate($template_name, $template_content, $message);
            //print_r($result);
            /*
            Array
            (
                [0] => Array
                    (
                        [email] => recipient.email@example.com
                        [status] => sent
                        [reject_reason] => hard-bounce
                        [_id] => abc123abc123abc123abc123abc123
                    )

            )
            */
        } catch (Mandrill_Error $e) {
            // Mandrill errors are thrown as exceptions
            echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
            // A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
            throw $e;
        }

    };

    function sendSchedulingInstructions($to, $requesterName, $requesterNewPosition, $requesterNewWeekend, $resolverName, $neededPosition, $resolverNewWeekend)
    {
        try {
            $mandrill = new Mandrill('1B54QfE4NOp7pkt0a7XCrA');
            $template_name = 'resolution-notification';
            $template_content = array(
                array(
                    'name' => 'example name',
                    'content' => 'example content'
                )
            );
            $message = array(
//        'html' => '<p>Example HTML content</p>',
//        'text' => 'Example text content',
                //'subject' => $subject,
                //'from_email' => 'productionScheduling@dev.floret.us',
                //'from_name' => 'Production Scheduling',
                'to' => array(
                    array(
                        'email' => $to,
                        //'name' => 'Recipient Name',
                        'type' => 'to'
                    )
                ),
                'headers' => array('Reply-To' => 'ben.ferris1@gmail.com'),
                'important' => false,
                'track_opens' => null,
                'track_clicks' => false,
                'auto_text' => null,
                'auto_html' => null,
                'inline_css' => null,
                'url_strip_qs' => null,
                'preserve_recipients' => null,
                'view_content_link' => null,
                //'bcc_address' => 'message.bcc_address@example.com',
                'tracking_domain' => null,
                'signing_domain' => null,
                'return_path_domain' => null,
                'merge' => true,
                'global_merge_vars' => array(
                    array(
                        'name' => 'neededweekend',
                        'content' => 'merge1 content'
                    )
                ),
                'merge_vars' => array(
                    array(
                        'rcpt' => $to,
                        'vars' => array(
                            array(
                                'name' => 'requestername',
                                'content' => $requesterName
                            ),
                            array(
                                'name' => 'requesternewposition',
                                'content' => $requesterNewPosition
                            ),
                            array(
                                'name' => 'requesternewweekend',
                                'content' => $requesterNewWeekend
                            ),
                            array(
                                'name' => 'resolvername',
                                'content' => $resolverName
                            ),
                            array(
                                'name' => 'neededposition',
                                'content' => $neededPosition
                            ),
                            array(
                                'name' => 'resolvernewweekend',
                                'content' => $resolverNewWeekend
                            )
                        )
                    )
                ),
            );
            $result = $mandrill->messages->sendTemplate($template_name, $template_content, $message);
            //print_r($result);
            /*
            Array
            (
                [0] => Array
                    (
                        [email] => recipient.email@example.com
                        [status] => sent
                        [reject_reason] => hard-bounce
                        [_id] => abc123abc123abc123abc123abc123
                    )

            )
            */
        } catch (Mandrill_Error $e) {
            // Mandrill errors are thrown as exceptions
            echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
            // A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
            throw $e;
        }
    }

    function sendPlainMessage($to, $subject, $message)
    {
        try {
            $mandrill = new Mandrill('1B54QfE4NOp7pkt0a7XCrA');
            //$template_name = 'Resolution Notification';


            $message = array(
                'html' => $message,
//        'text' => 'Example text content',
                'subject' => $subject,
                'from_email' => 'scheduling-notifications@dev.floret.us',
                'from_name' => 'Production Scheduling',
                'to' => array(
                    array(
                        'email' => $to,
                        //'name' => 'Recipient Name',
                        'type' => 'to'
                    )
                ),
                'headers' => array('Reply-To' => 'ben.ferris1@gmail.com'),
                'important' => false,
                'track_opens' => null,
                'track_clicks' => false,
                'auto_text' => null,
                'auto_html' => null,
                'inline_css' => null,
                'url_strip_qs' => null,
                'preserve_recipients' => null,
                'view_content_link' => null,
                //'bcc_address' => 'message.bcc_address@example.com',
                'tracking_domain' => null,
                'signing_domain' => null,
                'return_path_domain' => null,
                'merge' => true,
//            'global_merge_vars' => array(
//                array(
//                    'name' => 'neededweekend',
//                    'content' => 'merge1 content'
//                )
//            ),
//            'merge_vars' => array(
//                array(
//                    'rcpt' => $to,
//                    'vars' => array(
//                    )
//                )
//            ),
            );
            $result = $mandrill->messages->send($message);
            //print_r($result);
            /*
            Array
            (
                [0] => Array
                    (
                        [email] => recipient.email@example.com
                        [status] => sent
                        [reject_reason] => hard-bounce
                        [_id] => abc123abc123abc123abc123abc123
                    )

            )
            */
        } catch (Mandrill_Error $e) {
            // Mandrill errors are thrown as exceptions
            echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
            // A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
            throw $e;
        }
    }

    function sendCreationNotificationToRequester($to, $firstName, $weekendDate)
    {
        try {
            $mandrill = new Mandrill('1B54QfE4NOp7pkt0a7XCrA');
            $template_name = 'resolution-creation-notification';
            $template_content = array(
                array(
                    'name' => 'example name',
                    'content' => 'example content'
                )
            );
            $message = array(
//        'html' => '<p>Example HTML content</p>',
//        'text' => 'Example text content',
                //'subject' => $subject,
                'from_email' => 'productionScheduling@dev.floret.us',
                'from_name' => 'Production Scheduling',
                'to' => array(
                    array(
                        'email' => $to,
                        //'name' => 'Recipient Name',
                        'type' => 'to'
                    )
                ),
                'headers' => array('Reply-To' => 'ben.ferris1@gmail.com'),
                'important' => false,
                'track_opens' => null,
                'track_clicks' => false,
                'auto_text' => null,
                'auto_html' => null,
                'inline_css' => null,
                'url_strip_qs' => null,
                'preserve_recipients' => null,
                'view_content_link' => null,
                //'bcc_address' => 'message.bcc_address@example.com',
                'tracking_domain' => null,
                'signing_domain' => null,
                'return_path_domain' => null,
                'merge' => true,
                'global_merge_vars' => array(
                    array(
                        'name' => 'neededweekend',
                        'content' => 'merge1 content'
                    )
                ),
                'merge_vars' => array(
                    array(
                        'rcpt' => $to,
                        'vars' => array(
                            array(
                                'name' => 'weekenddate',
                                'content' => $weekendDate
                            ),
                            array(
                                'name' => 'firstname',
                                'content' => $firstName
                            )
                        )
                    )
                ),
            );
            $result = $mandrill->messages->sendTemplate($template_name, $template_content, $message);
            //print_r($result);
            /*
            Array
            (
                [0] => Array
                    (
                        [email] => recipient.email@example.com
                        [status] => sent
                        [reject_reason] => hard-bounce
                        [_id] => abc123abc123abc123abc123abc123
                    )

            )
            */
        } catch (Mandrill_Error $e) {
            // Mandrill errors are thrown as exceptions
            sendPlainMessage('ben.ferris1@gmail.com', 'Mandrill Error', $e->getMessage());
            echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
            // A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
            throw $e;
        }

    }

    function sendCancellationNotification($to, $name, $position, $weekendDate)
    {
        try {
            $mandrill = new Mandrill('1B54QfE4NOp7pkt0a7XCrA');
            $template_name = 'cancellation-notification';
            $template_content = array(
                array(
                    'name' => 'example name',
                    'content' => 'example content'
                )
            );
            $message = array(
//        'html' => '<p>Example HTML content</p>',
//        'text' => 'Example text content',
                //'subject' => $subject,
                //'from_email' => 'productionScheduling@dev.floret.us',
                //'from_name' => 'Production Scheduling',
                'to' => array(
                    array(
                        'email' => $to,
                        //'name' => 'Recipient Name',
                        'type' => 'to'
                    )
                ),
                'headers' => array('Reply-To' => 'ben.ferris1@gmail.com'),
                'important' => false,
                'track_opens' => null,
                'track_clicks' => false,
                'auto_text' => null,
                'auto_html' => null,
                'inline_css' => null,
                'url_strip_qs' => null,
                'preserve_recipients' => null,
                'view_content_link' => null,
                //'bcc_address' => 'message.bcc_address@example.com',
                'tracking_domain' => null,
                'signing_domain' => null,
                'return_path_domain' => null,
                'merge' => true,
                'global_merge_vars' => array(
                    array(
                        'name' => 'neededweekend',
                        'content' => 'merge1 content'
                    )
                ),
                'merge_vars' => array(
                    array(
                        'rcpt' => $to,
                        'vars' => array(
                            array(
                                'name' => 'weekenddate',
                                'content' => $weekendDate
                            ),
                            array(
                                'name' => 'neededposition',
                                'content' => $position
                            ),
                            array(
                                'name' => 'name',
                                'content' => $name
                            )
                        )
                    )
                ),
            );
            $result = $mandrill->messages->sendTemplate($template_name, $template_content, $message);
            //print_r($result);
            /*
            Array
            (
                [0] => Array
                    (
                        [email] => recipient.email@example.com
                        [status] => sent
                        [reject_reason] => hard-bounce
                        [_id] => abc123abc123abc123abc123abc123
                    )

            )
            */
        } catch (Mandrill_Error $e) {
            // Mandrill errors are thrown as exceptions
            sendPlainMessage('ben.ferris1@gmail.com', 'Mandrill Error', $e->getMessage());
            echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
            // A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
            throw $e;
        }
    }

    if (isset($_GET['action']) && $_GET['action'] == "sendError") {
        sendPlainMessage('benferris2@gmail.com', 'Scheduling Error Report', $_GET['message']);
    }

}