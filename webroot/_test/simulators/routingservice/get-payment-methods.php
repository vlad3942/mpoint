<?php
// Require Global Include File
require_once '../../../../webroot/inc/include.php';
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."/simpledom.php");

$obj_DOM = simpledom_load_string(file_get_contents('php://input') );

$xml = '';
if(empty($obj_DOM->client_info->client_id) === false && empty($obj_DOM->transaction->amount->country_id) === false && empty($obj_DOM->transaction->amount->currency_id) == false)
{
    header('HTTP/1.1 200 OK');
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    $xml = '<payment_method_search_response>
			<payment_methods>
				<payment_method>
					<id>17</id>
					<psp_type>2</psp_type>
					<preference/>
				</payment_method>
				<payment_method>
					<id>18</id>
					<psp_type>1</psp_type>
					<preference/>
				</payment_method>
               <payment_method>
                    <id>15</id>
                    <psp_type>3</psp_type>
                    <preference>2</preference>
                    <state_id>1</state_id>
                    <card_schemes>
                        <card_scheme>
                            <id>7</id>
                        </card_scheme>
                        <card_scheme>
                            <id>8</id>
                        </card_scheme>
                    </card_schemes>
                </payment_method>
			</payment_methods>
		</payment_method_search_response>';
    echo $xml;
}else {
    header('HTTP/1.1 200 OK');
    header("Content-Type: text/xml; charset=\"UTF-8\"");

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<root>';
    echo '</root>';
}



