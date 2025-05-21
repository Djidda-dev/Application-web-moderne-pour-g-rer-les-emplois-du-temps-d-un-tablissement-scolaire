<?php
header('Content-Type: xml');

$emploi = [
    'classes' => 'Licence 1',
    'cours' => [
        ['jour' => 'lundi', 'debut' => '08:30', 'fin' => '10:00', 'prof' => 'A', 'module' => 'M1', 'salle' => 'lab4'],
        ['jour' => 'lundi', 'debut' => '10:15', 'fin' => '11:45', 'prof' => 'B', 'module' => 'M2', 'salle' => 'londres']
    ]
];

$xml = new SimpleXMLElement('<emploi/>');
$xml->addAttribute('classes', $emploi['classes']);

foreach ($emploi['seances'] as $seance) {
    $seanceElement = $xml->addChild('cours');
    $seanceElement->addAttribute('jour', $seance['jour']);
    $seanceElement->addAttribute('debut', $seance['debut']);
    $seanceElement->addAttribute('fin', $seance['fin']);
    $seanceElement->addAttribute('prof', $seance['prof']);
    $seanceElement->addAttribute('module', $seance['module']);
    $seanceElement->addAttribute('salle', $seance['salle']);
}

echo $xml->asXML();
?>