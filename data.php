<?php
require_once 'lib.php';

function getDataFromService()
{
    $object = array(
        'id' => 45,
        'status' => 3,
        'parameter' => (object)array(
            'report_class' => 4,
            'report_template' => 15,
            'delayed_result' => 1,
            'device_role_type_id' => 1,
            'object' => 91180,
            'object_name' => 'Министерство социального развития Московской области',
            'report_class_name' => 'Сводный отчет по услугам',
            'report_template_name' => 'Новый сводный отчет',
            'time_start' => 1460840400,
            'time_end' => 1461099600,
            'time_request' => 1462607827,
            'report_object_type_id' => 440
        ),
        'time_create' => '2016-05-07 10:55:47',
        'time_start' => 1460840400,
        'time_end' => 1461099600,

        'report_template' => (object)array(
            'id' => 15,
            'name' => 'Новый сводный отчет',
            'description' => null,
            'time_create' => '2016-04-26 16:12:20',
            'time_change' => '2016-04-26 16:12:23',
            'report_class' => 4,
            'metric' => '[{
		"code": "code#1",
	"name": "К-т доступ ности услуги",
	"report_threshold_group": "1",
	"threshold_name": null,
	"items": [{
			"index": 0,
		"algorithm": "AVG(availability_index)",
		"format_type": 1,
		"format": "%s",
		"comparison_value": "0.995",
		"comparison_operator": "<"
	},
	{
		"index": 1,
		"algorithm": "SUM(duration)",
		"format_type": 3
	}]
},
{
	"code": "code#2",
	"name": "Время задержки пакетов, не более, мс",
	"report_threshold_group": "3",
	"threshold_name": null,
	"items": [{
	"index": 0,
		"format_type": 1,
		"format": "%s",
		"algorithm": "MAX(max)"
	},
	{
		"index": 1,
		"format_type": 3,
		"algorithm": "SUM(duration)",
		"hidden":1

	}]
},
{
	"code": "code#3",
	"name": "Вариация времени задержки пакетов, не более, мс",
	"report_threshold_group": "4",
	"threshold_name": null,
	"items": [{
	"index":0,
		"algorithm": "MAX(max)",
		"format_type": 1,
		"format": "%s"
	},
	{
		"index":1,
		"algorithm": "SUM(duration)",
		"format_type": 3,
		"hidden":1
	}]
},
{
	"code": "code#4",
	"name": "Потери пакетов, %, не более",
	"report_threshold_group": "2",
	"threshold_name": null,
	"items": [{
	"index":0,
		"algorithm": "MAX(max)",
		"format_type": 1,
		"format": "%s"
	},
	{
		"index":1,
		"algorithm": "SUM(duration)",
		"format_type": 3,
		"hidden":1
	}]
},
{
	"code": "synthetic_test",
	"name": "Тестим синтетик",
	"threshold_name": null,
	"items": [{
	"index":0,
		"format_type": 3,
		"format": "%s",
		"value_expression":
		{
			"operator":"+",
			"arguments":[ "${code#2.2}", "${code#3.2}" ]
		},
		"threshold_expression":
		{
			"operator":"*",
			"arguments":[ "${period}", "0.995"]
		},
		"comparison_operator": ">="
	}]
}]',

            'fields' => '{
		"administration": {
			"name": "Пользователь"
	},
	"region": {
			"name": "Регион"
	},
	"office": {
			"name": "Наименование объекта"
	},
	"city": {
			"name": "Город"
	},
	"street": {
			"name": "Улица"
	},
	"house": {
			"name": "Дом"
	},
	"service_item": {
			"id": "id услуги",
		"name": "Услуга"
	},
	"service": {
			"id": "id сервиса",
		"name": "Вид услуги"
	},
	"link": {
			"bw": "Скорость, Мбит"
	}
}',
            'parameter' => '{
		"header":"Министерство государственного управления, информационных технологий и связи Московской области",
"title":"Сводный отчет по качеству оказываемых услуг согласно Государственного контракта № 0148200005414001014 на оказание телекоммуникационных услуг для центральных исполнительных органов государственной власти и государственных органов Московской области в период 2015-2017 гг.",
"summary_table": "1"
}',
            'period' => 2
        ),


        'data' => (object)array(
            'summary' => array(
                0 => (object)array(
                    'service_id' => 91159,
                    'service_name' => 'Internet',
                    'total_items' => 75,
                    'items_with_errors' => 75,
                    'thresholds' => (object)array(
                        'code#1' => (object)array(
                            'value' => array(
                                0 => 100,
                                1 => 100
                            ),
                            'operator' => array(
                                0 => '<',
                                1 => '<'
                            )
                        ),
                        'code#2' => (object)array(
                            'value' => array(
                                0 => 250,
                                1 => 250
                            ),
                            'operator' => array(
                                0 => '>=',
                                1 => '>='
                            )
                        ),
                        'code#3' => (object)array(
                            'value' => array(
                                0 => 100,
                                1 => 100
                            ),
                            'operator' => array(
                                0 => '>=',
                                1 => '>='
                            )
                        ),
                        'code#4' => (object)array(
                            'value' => array(
                                0 => 0.1,
                                1 => 0.1
                            ),
                            'operator' => array(
                                0 => '>=',
                                1 => '>='
                            )
                        ),
                        'synthetic_test' => (object)array(
                            'value' => array(
                                0 => 257904
                            ),
                            'operator' => array(
                                0 => '>='
                            )
                        )
                    )
                ),

                1 => (object)array(
                    'service_id' => 91160,
                    'service_name' => 'ВЦКС',
                    'total_items' => 0,
                    'items_with_errors' => 0,
                    'thresholds' => (object)array(
                        'code#1' => (object)array(
                            'value' => array(
                                0 => 100,
                                1 => 100
                            ),
                            'operator' => array(
                                0 => '<',
                                1 => '<'
                            )
                        ),
                        'code#2' => (object)array(
                            'value' => array(
                                0 => 100,
                                1 => 100
                            ),
                            'operator' => array(
                                0 => '>=',
                                1 => '>='
                            )
                        ),
                        'code#3' => (object)array(
                            'value' => array(
                                0 => 50,
                                1 => 50
                            ),
                            'operator' => array(
                                0 => '>=',
                                1 => '>='
                            )
                        ),
                        'code#4' => (object)array(
                            'value' => array(
                                0 => 0.1,
                                1 => 0.1
                            ),
                            'operator' => array(
                                0 => '>=',
                                1 => '>='
                            )
                        ),
                        'synthetic_test' => (object)array(
                            'value' => array(
                                0 => 257904
                            ),
                            'operator' => array(
                                0 => '>='
                            )
                        )
                    )
                ),

                2 => (object)array(
                    'service_id' => 91161,
                    'service_name' => 'L3',
                    'total_items' => 0,
                    'items_with_errors' => 0,
                    'thresholds' => (object)array(
                        'code#1' => (object)array(
                            'value' => array(
                                0 => 100,
                                1 => 100
                            ),
                            'operator' => array(
                                0 => '<',
                                1 => '<'
                            )
                        ),
                        'code#2' => (object)array(
                            'value' => array(
                                0 => 100,
                                1 => 100
                            ),
                            'operator' => array(
                                0 => '>=',
                                1 => '>='
                            )
                        ),
                        'code#3' => (object)array(
                            'value' => array(
                                0 => 50,
                                1 => 50
                            ),
                            'operator' => array(
                                0 => '>=',
                                1 => '>='
                            )
                        ),
                        'code#4' => (object)array(
                            'value' => array(
                                0 => 0.1,
                                1 => 0.1
                            ),
                            'operator' => array(
                                0 => '>=',
                                1 => '>='
                            )
                        ),
                        'synthetic_test' => (object)array(
                            'value' => array(
                                0 => 257904
                            ),
                            'operator' => array(
                                0 => '>='
                            )
                        )
                    )
                ),

                3 => (object)array(
                    'service_id' => 91162,
                    'service_name' => 'L2',
                    'total_items' => 7,
                    'items_with_errors' => 7,
                    'thresholds' => (object)array(
                        'code#1' => (object)array(
                            'value' => array(
                                0 => null,
                                1 => null
                            ),
                            'operator' => array(
                                0 => null,
                                1 => null
                            )
                        ),
                        'code#2' => (object)array(
                            'value' => array(
                                0 => null,
                                1 => null
                            ),
                            'operator' => array(
                                0 => null,
                                1 => null
                            )
                        ),
                        'code#3' => (object)array(
                            'value' => array(
                                0 => null,
                                1 => null
                            ),
                            'operator' => array(
                                0 => null,
                                1 => null
                            )
                        ),
                        'code#4' => (object)array(
                            'value' => array(
                                0 => null,
                                1 => null
                            ),
                            'operator' => array(
                                0 => null,
                                1 => null
                            )
                        ),
                        'synthetic_test' => (object)array(
                            'value' => array(
                                0 => 257904
                            ),
                            'operator' => array(
                                0 => '>='
                            )
                        )
                    )
                )
            ),

            'data' => array(
                0 => (object)array(
                    'equipment' => 94621,
                    'device' => 96752,
                    'link' => 94622,
                    'code#1' => Array
                    (
                        0 => 0.508,
                        1 => 127360.000
                    ),

                    'code#2' => array
                    (
                        0 => null,
                        1 => 0.000
                    ),

                    'code#3' => array
                    (
                        0 => null,
                        1 => 0.000
                    ),

                    'code#4' => array
                    (
                        0 => null,
                        1 => 0.000
                    ),

                    'synthetic_test' => array
                    (
                        0 => 0
                    ),

                    'administration.name' => 'Министерство социального развития Московской области',
                    'region.name' => 'Московская область',
                    'office.name' => 'Ступинское Управление социальной защиты населения',
                    'city.name' => 'Ступино город',
                    'street.name' => 'Победы проспект',
                    'house.name' => 'дом 51',
                    'service_item . id' => 91163,
                    'service_item . name' => 'Internet',
                    'service.id' => 91159,
                    'service . name' => 'Internet',
                    'link.bw' => 10
                ),

                1 => (object)array(
                    'equipment' => 94619,
                    'device' => 96753,
                    'link' => 94620,
                    'code#1' => Array
                    (
                        0 => 0.508,
                        1 => 127360.000
                    ),

                    'code#2' => array
                    (
                        0 => null,
                        1 => 0.000
                    ),

                    'code#3' => array
                    (
                        0 => null,
                        1 => 0.000
                    ),

                    'code#4' => array
                    (
                        0 => null,
                        1 => 5760.000
                    ),

                    'synthetic_test' => array
                    (
                        0 => 0
                    ),

                    'administration.name' => 'Министерство социального развития Московской области',
                    'region.name' => 'Московская область',
                    'office.name' => 'Ступинское Управление социальной защиты населения',
                    'city.name' => 'Ступино город',
                    'street.name' => 'Андропова улица',
                    'house.name' => 'дом 30/23',
                    'service_item . id' => 91163,
                    'service_item . name' => 'Internet',
                    'service.id' => 91159,
                    'service . name' => 'Internet',
                    'link.bw' => 10
                )
            )
        )

    );


    $object = (object)$object;

    return $object;
}


/*
[Sat, 07 May 2016 14:47:52 +0300][/index.php][10.200.1.93] rrrrrrrrrrr
[Sat, 07 May 2016 14:47:52 +0300][/index.php][10.200.1.93] stdClass Object
(
    [id] => 45
    [status] => 3
    [parameter] => stdClass Object
        (
            [report_class] => 4
            [report_template] => 15
            [delayed_result] => 1
            [device_role_type_id] => 1
            [object] => 91180
            [object_name] => Министерство социального развития Московской области
            [report_class_name] => Сводный отчет по услугам
            [report_template_name] => Новый сводный отчет
            [time_start] => 1460840400
            [time_end] => 1461099600
            [time_request] => 1462607827
            [report_object_type_id] => 440
        )

    [time_create] => 2016-05-07 10:55:47
    [time_start] => 1460840400
    [time_end] => 1461099600


    [report_template] => stdClass Object
        (
            [id] => 15
            [name] => Новый сводный отчет
            [description] =>
            [time_create] => 2016-04-26 16:12:20
            [time_change] => 2016-04-26 16:12:23
            [report_class] => 4
            [metric] => [{
	"code": "code#1",
	"name": "К-т доступ ности услуги",
	"report_threshold_group": "1",
	"threshold_name": null,
	"items": [{
		"index": 0,
		"algorithm": "AVG(availability_index)",
		"format_type": 1,
		"format": "%s",
		"comparison_value": "0.995",
		"comparison_operator": "<"
	},
	{
		"index": 1,
		"algorithm": "SUM(duration)",
		"format_type": 3
	}]
},
{
	"code": "code#2",
	"name": "Время задержки пакетов, не более, мс",
	"report_threshold_group": "3",
	"threshold_name": null,
	"items": [{
		"index": 0,
		"format_type": 1,
		"format": "%s",
		"algorithm": "MAX(max)"
	},
	{
		"index": 1,
		"format_type": 3,
		"algorithm": "SUM(duration)",
		"hidden":1

	}]
},
{
	"code": "code#3",
	"name": "Вариация времени задержки пакетов, не более, мс",
	"report_threshold_group": "4",
	"threshold_name": null,
	"items": [{
		"index":0,
		"algorithm": "MAX(max)",
		"format_type": 1,
		"format": "%s"
	},
	{
		"index":1,
		"algorithm": "SUM(duration)",
		"format_type": 3,
		"hidden":1
	}]
},
{
	"code": "code#4",
	"name": "Потери пакетов, %, не более",
	"report_threshold_group": "2",
	"threshold_name": null,
	"items": [{
		"index":0,
		"algorithm": "MAX(max)",
		"format_type": 1,
		"format": "%s"
	},
	{
		"index":1,
		"algorithm": "SUM(duration)",
		"format_type": 3,
		"hidden":1
	}]
},
{
	"code": "synthetic_test",
	"name": "Тестим синтетик",
	"threshold_name": null,
	"items": [{
		"index":0,
		"format_type": 3,
		"format": "%s",
		"value_expression":
		{
			"operator":"+",
			"arguments":[ "${code#2.2}", "${code#3.2}" ]
		},
		"threshold_expression":
		{
			"operator":"*",
			"arguments":[ "${period}", "0.995"]
		},
		"comparison_operator": ">="
	}]
}]
            [fields] => {
	"administration": {
		"name": "Пользователь"
	},
	"region": {
		"name": "Регион"
	},
	"office": {
		"name": "Наименование объекта"
	},
	"city": {
		"name": "Город"
	},
	"street": {
		"name": "Улица"
	},
	"house": {
		"name": "Дом"
	},
	"service_item": {
		"id": "id услуги",
		"name": "Услуга"
	},
	"service": {
		"id": "id сервиса",
		"name": "Вид услуги"
	},
	"link": {
		"bw": "Скорость, Мбит"
	}
}
            [parameter] => {
"header":"Министерство государственного управления, информационных технологий и связи Московской области",
"title":"Сводный отчет по качеству оказываемых услуг согласно Государственного контракта № 0148200005414001014 на оказание телекоммуникационных услуг для центральных исполнительных органов государственной власти и государственных органов Московской области в период 2015-2017 гг.",
"summary_table": "1"
}
            [period] => 2
        )

    [data] => stdClass Object
        (
            [summary] => Array
                (
                    [0] => stdClass Object
                        (
                            [service_id] => 91159
                            [service_name] => Internet
                            [total_items] => 75
                            [items_with_errors] => 75
                            [thresholds] => stdClass Object
                                (
                                    [code#1] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] => 100
                                                    [1] => 100
                                                )

                                            [operator] => Array
                                                (
                                                    [0] => <
                                                    [1] => <
                                                )

                                        )

                                    [code#2] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] => 250
                                                    [1] => 250
                                                )

                                            [operator] => Array
                                                (
                                                    [0] => >=
                                                    [1] => >=
                                                )

                                        )

                                    [code#3] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] => 100
                                                    [1] => 100
                                                )

                                            [operator] => Array
                                                (
                                                    [0] => >=
                                                    [1] => >=
                                                )

                                        )

                                    [code#4] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] => 0.1
                                                    [1] => 0.1
                                                )

                                            [operator] => Array
                                                (
                                                    [0] => >=
                                                    [1] => >=
                                                )

                                        )

                                    [synthetic_test] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] => 257904
                                                )

                                            [operator] => Array
                                                (
                                                    [0] => >=
                                                )

                                        )

                                )

                        )

                    [1] => stdClass Object
                        (
                            [service_id] => 91160
                            [service_name] => ВЦКС
                            [total_items] => 0
                            [items_with_errors] => 0
                            [thresholds] => stdClass Object
                                (
                                    [code#1] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] => 100
                                                    [1] => 100
                                                )

                                            [operator] => Array
                                                (
                                                    [0] => <
                                                    [1] => <
                                                )

                                        )

                                    [code#2] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] => 100
                                                    [1] => 100
                                                )

                                            [operator] => Array
                                                (
                                                    [0] => >=
                                                    [1] => >=
                                                )

                                        )

                                    [code#3] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] => 50
                                                    [1] => 50
                                                )

                                            [operator] => Array
                                                (
                                                    [0] => >=
                                                    [1] => >=
                                                )

                                        )

                                    [code#4] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] => 0.1
                                                    [1] => 0.1
                                                )

                                            [operator] => Array
                                                (
                                                    [0] => >=
                                                    [1] => >=
                                                )

                                        )

                                    [synthetic_test] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] => 257904
                                                )

                                            [operator] => Array
                                                (
                                                    [0] => >=
                                                )

                                        )

                                )

                        )

                    [2] => stdClass Object
                        (
                            [service_id] => 91161
                            [service_name] => L3
                            [total_items] => 0
                            [items_with_errors] => 0
                            [thresholds] => stdClass Object
                                (
                                    [code#1] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] => 100
                                                    [1] => 100
                                                )

                                            [operator] => Array
                                                (
                                                    [0] => <
                                                    [1] => <
                                                )

                                        )

                                    [code#2] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] => 100
                                                    [1] => 100
                                                )

                                            [operator] => Array
                                                (
                                                    [0] => >=
                                                    [1] => >=
                                                )

                                        )

                                    [code#3] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] => 50
                                                    [1] => 50
                                                )

                                            [operator] => Array
                                                (
                                                    [0] => >=
                                                    [1] => >=
                                                )

                                        )

                                    [code#4] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] => 0.1
                                                    [1] => 0.1
                                                )

                                            [operator] => Array
                                                (
                                                    [0] => >=
                                                    [1] => >=
                                                )

                                        )

                                    [synthetic_test] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] => 257904
                                                )

                                            [operator] => Array
                                                (
                                                    [0] => >=
                                                )

                                        )

                                )

                        )

                    [3] => stdClass Object
                        (
                            [service_id] => 91162
                            [service_name] => L2
                            [total_items] => 7
                            [items_with_errors] => 7
                            [thresholds] => stdClass Object
                                (
                                    [code#1] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] =>
                                                    [1] =>
                                                )

                                            [operator] => Array
                                                (
                                                    [0] =>
                                                    [1] =>
                                                )

                                        )

                                    [code#2] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] =>
                                                    [1] =>
                                                )

                                            [operator] => Array
                                                (
                                                    [0] =>
                                                    [1] =>
                                                )

                                        )

                                    [code#3] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] =>
                                                    [1] =>
                                                )

                                            [operator] => Array
                                                (
                                                    [0] =>
                                                    [1] =>
                                                )

                                        )

                                    [code#4] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] =>
                                                    [1] =>
                                                )

                                            [operator] => Array
                                                (
                                                    [0] =>
                                                    [1] =>
                                                )

                                        )

                                    [synthetic_test] => stdClass Object
                                        (
                                            [value] => Array
                                                (
                                                    [0] => 257904
                                                )

                                            [operator] => Array
                                                (
                                                    [0] => >=
                                                )

                                        )

                                )

                        )

                )

            [data] => Array
                (
                    [0] => stdClass Object
                        (
                            [equipment] => 94621
                            [device] => 96752
                            [link] => 94622
                            [code#1] => Array
                                (
                                    [0] => 0.508
                                    [1] => 127360.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Ступинское Управление социальной защиты населения
                            [city.name] => Ступино город
                            [street.name] => Победы проспект
                            [house.name] => дом 51
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 10
                        )

                    [1] => stdClass Object
                        (
                            [equipment] => 94619
                            [device] => 96753
                            [link] => 94620
                            [code#1] => Array
                                (
                                    [0] => 0.508
                                    [1] => 127360.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 5760.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Ступинское Управление социальной защиты населения
                            [city.name] => Ступино город
                            [street.name] => Андропова улица
                            [house.name] => дом 30/23
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 10
                        )

                    [2] => stdClass Object
                        (
                            [equipment] => 94577
                            [device] => 96752
                            [link] => 94578
                            [code#1] => Array
                                (
                                    [0] => 0.508
                                    [1] => 127360.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 8832.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Серпуховское районное Управление социальной защиты населения
                            [city.name] => Серпухов город
                            [street.name] => Советская улица
                            [house.name] => дом 88
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 20
                        )

                    [3] => stdClass Object
                        (
                            [equipment] => 94553
                            [device] => 96752
                            [link] => 94554
                            [code#1] => Array
                                (
                                    [0] => 0.509
                                    [1] => 126976.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 512.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Серпуховское районное Управление социальной защиты населения
                            [city.name] => Серпухов город
                            [street.name] => Советская улица
                            [house.name] => дом 19
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 20
                        )

                    [4] => stdClass Object
                        (
                            [equipment] => 94627
                            [device] => 96753
                            [link] => 94628
                            [code#1] => Array
                                (
                                    [0] => 0.506
                                    [1] => 127744.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 2304.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Серебряно-Прудское Управление социальной защиты населения
                            [city.name] => Серебряные Пруды поселок
                            [street.name] => Привокзальная улица
                            [house.name] => дом 2
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 10
                        )

                    [5] => stdClass Object
                        (
                            [equipment] => 94681
                            [device] => 96753
                            [link] => 94682
                            [code#1] => Array
                                (
                                    [0] => 0.508
                                    [1] => 127360.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 2944.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Егорьевское управление социальной защиты населения
                            [city.name] => Егорьевск город
                            [street.name] => Советская улица
                            [house.name] => дом 104
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 5
                        )

                    [6] => stdClass Object
                        (
                            [equipment] => 94775
                            [device] => 96752
                            [link] => 94776
                            [code#1] => Array
                                (
                                    [0] => 0.510
                                    [1] => 126976.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 3584.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Жуковское управление социальной защиты населения
                            [city.name] => Жуковский город
                            [street.name] => Советская улица
                            [house.name] => дом 6
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 20
                        )

                    [7] => stdClass Object
                        (
                            [equipment] => 94727
                            [device] => 96752
                            [link] => 94728
                            [code#1] => Array
                                (
                                    [0] => 0.514
                                    [1] => 125824.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 50176.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Мытищинское управление социальной защиты населения
                            [city.name] => Мытищи город
                            [street.name] => Новомытищинский проспект
                            [house.name] => дом 82, корпус 7
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 30
                        )

                    [8] => stdClass Object
                        (
                            [equipment] => 94707
                            [device] => 96752
                            [link] => 94708
                            [code#1] => Array
                                (
                                    [0] => 0.175
                                    [1] => 213760.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Отдел социальной защиты населения г Рошаль
                            [city.name] => Рошаль город
                            [street.name] => Фридриха Энгельса улица
                            [house.name] => дом 16, корпус 2
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 10
                        )

                    [9] => stdClass Object
                        (
                            [equipment] => 94477
                            [device] => 96752
                            [link] => 94478
                            [code#1] => Array
                                (
                                    [0] => 0.508
                                    [1] => 127360.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 43264.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Отдел социальной защиты населения г Лосино-Петровский
                            [city.name] => Лосино-Петровский город
                            [street.name] => Октябрьская улица
                            [house.name] => дом 6
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 10
                        )

                    [10] => stdClass Object
                        (
                            [equipment] => 94467
                            [device] => 96753
                            [link] => 94468
                            [code#1] => Array
                                (
                                    [0] => 0.508
                                    [1] => 127360.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 14848.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Железнодорожное управление социальной защиты населения
                            [city.name] => Железнодорожный город
                            [street.name] => Саввинское шоссе
                            [house.name] => дом 4
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 20
                        )

                    [11] => stdClass Object
                        (
                            [equipment] => 94385
                            [device] => 96752
                            [link] => 94386
                            [code#1] => Array
                                (
                                    [0] => 0.508
                                    [1] => 127360.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 384.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Отдел социальной защиты населения п Черноголовка
                            [city.name] => Черноголовка город
                            [street.name] => Институтский проспект
                            [house.name] => дом 8
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 10
                        )

                    [12] => stdClass Object
                        (
                            [equipment] => 94353
                            [device] => 96752
                            [link] => 94354
                            [code#1] => Array
                                (
                                    [0] => 0.508
                                    [1] => 127360.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 1536.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Озерское управление социальной защиты населения
                            [city.name] => Озёры город
                            [street.name] => Ленина улица
                            [house.name] => дом 24
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 10
                        )

                    [13] => stdClass Object
                        (
                            [equipment] => 94349
                            [device] => 96752
                            [link] => 94350
                            [code#1] => Array
                                (
                                    [0] => 0.508
                                    [1] => 127360.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 19712.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Раменское управление социальной защиты населения
                            [city.name] => Раменское город
                            [street.name] => Железнодорожный проезд
                            [house.name] => дом 7
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 30
                        )

                    [14] => stdClass Object
                        (
                            [equipment] => 94393
                            [device] => 96752
                            [link] => 94394
                            [code#1] => Array
                                (
                                    [0] => 0.508
                                    [1] => 127360.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 1920.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Шатурское Управление социальной защиты населения
                            [city.name] => Шатура город
                            [street.name] => Интернациональная улица
                            [house.name] => дом 15
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 20
                        )

                    [15] => stdClass Object
                        (
                            [equipment] => 94413
                            [device] => 96752
                            [link] => 94414
                            [code#1] => Array
                                (
                                    [0] => 0.508
                                    [1] => 127360.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 1408.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Зарайское управление социальной защиты населения
                            [city.name] => Зарайск город
                            [street.name] => Мерецкова улица
                            [house.name] => дом 1
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 10
                        )

                    [16] => stdClass Object
                        (
                            [equipment] => 94453
                            [device] => 96752
                            [link] => 94454
                            [code#1] => Array
                                (
                                    [0] => 0.175
                                    [1] => 213760.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Луховицкое управление социальной защиты населения
                            [city.name] => Луховицы город
                            [street.name] => Советская улица
                            [house.name] => дом 7
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 20
                        )

                    [17] => stdClass Object
                        (
                            [equipment] => 94441
                            [device] => 96752
                            [link] => 94442
                            [code#1] => Array
                                (
                                    [0] => 0.508
                                    [1] => 127360.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 1664.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Коломенское районное управление социальной защиты населения
                            [city.name] => Коломна город
                            [street.name] => Зайцева улица
                            [house.name] => дом 40
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 20
                        )

                    [18] => stdClass Object
                        (
                            [equipment] => 94417
                            [device] => 96752
                            [link] => 94418
                            [code#1] => Array
                                (
                                    [0] => 0.508
                                    [1] => 127360.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 1920.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Коломенское районное управление социальной защиты населения
                            [city.name] => Коломна город
                            [street.name] => Чкалова улица
                            [house.name] => дом 17
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 20
                        )

                    [19] => stdClass Object
                        (
                            [equipment] => 94843
                            [device] => 96752
                            [link] => 94844
                            [code#1] => Array
                                (
                                    [0] => 0.510
                                    [1] => 126976.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 5632.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Егорьевское управление социальной защиты населения
                            [city.name] => Егорьевск город
                            [street.name] => Гражданская улица
                            [house.name] => дом 30/46
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 10
                        )

                    [20] => stdClass Object
                        (
                            [equipment] => 94917
                            [device] => 96753
                            [link] => 94918
                            [code#1] => Array
                                (
                                    [0] => 0.510
                                    [1] => 126976.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 2048.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Климовское управление социальной защиты населения
                            [city.name] => Климовск город
                            [street.name] => Ленина улица
                            [house.name] => дом 27
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 20
                        )

                    [21] => stdClass Object
                        (
                            [equipment] => 95463
                            [device] => 96753
                            [link] => 95464
                            [code#1] => Array
                                (
                                    [0] => 0.000
                                    [1] => 172544.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Химкинское Управление социальной защиты населения
                            [city.name] => Химки город
                            [street.name] => Пролетарская улица
                            [house.name] => дом 25
                            [service_item.id] => 91168
                            [service_item.name] => L2 VPN_MINCOTSZ_MO
                            [service.id] => 91162
                            [service.name] => L2
                            [link.bw] => 2
                        )

                    [22] => stdClass Object
                        (
                            [equipment] => 95459
                            [device] => 96752
                            [link] => 95460
                            [code#1] => Array
                                (
                                    [0] => 0.510
                                    [1] => 126976.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 11008.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Наро-Фоминское управление социальной защиты населения
                            [city.name] => Наро-Фоминск город
                            [street.name] => Ленина улица
                            [house.name] => дом 24
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 20
                        )

                    [23] => stdClass Object
                        (
                            [equipment] => 95359
                            [device] => 96752
                            [link] => 95360
                            [code#1] => Array
                                (
                                    [0] => 0.510
                                    [1] => 126976.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 1664.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Люберецкое управление социальной защиты населения
                            [city.name] => Люберцы город
                            [street.name] => Мира улица
                            [house.name] => дом 7А
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 30
                        )

                    [24] => stdClass Object
                        (
                            [equipment] => 95331
                            [device] => 96752
                            [link] => 95332
                            [code#1] => Array
                                (
                                    [0] => 0.510
                                    [1] => 126976.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 5504.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Рузское управление социальной защиты населения
                            [city.name] => Руза город
                            [street.name] => Социалистическая улица
                            [house.name] => дом 59
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 20
                        )

                    [25] => stdClass Object
                        (
                            [equipment] => 95517
                            [device] => 96752
                            [link] => 95518
                            [code#1] => Array
                                (
                                    [0] => 0.508
                                    [1] => 127360.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 33536.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Ивантеевское управление социальной защиты населения
                            [city.name] => Ивантеевка город
                            [street.name] => Центральный проезд
                            [house.name] => дом 14
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 20
                        )

                    [26] => stdClass Object
                        (
                            [equipment] => 95519
                            [device] => 96752
                            [link] => 95520
                            [code#1] => Array
                                (
                                    [0] => 0.500
                                    [1] => 86272.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Дмитровское управление социальной защиты населения
                            [city.name] => Дмитров город
                            [street.name] => Профессиональная улица
                            [house.name] => дом 1А
                            [service_item.id] => 91168
                            [service_item.name] => L2 VPN_MINCOTSZ_MO
                            [service.id] => 91162
                            [service.name] => L2
                            [link.bw] => 2
                        )

                    [27] => stdClass Object
                        (
                            [equipment] => 95525
                            [device] => 96752
                            [link] => 95526
                            [code#1] => Array
                                (
                                    [0] => 0.504
                                    [1] => 85504.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Ступинское Управление социальной защиты населения
                            [city.name] => Ступино город
                            [street.name] => Андропова улица
                            [house.name] => дом 30/23
                            [service_item.id] => 91168
                            [service_item.name] => L2 VPN_MINCOTSZ_MO
                            [service.id] => 91162
                            [service.name] => L2
                            [link.bw] => 2
                        )

                    [28] => stdClass Object
                        (
                            [equipment] => 95523
                            [device] => 96752
                            [link] => 95524
                            [code#1] => Array
                                (
                                    [0] => 0.000
                                    [1] => 172672.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Одинцовское управление социальной защиты населения
                            [city.name] => Одинцово город
                            [street.name] => Маршала Жукова улица
                            [house.name] => дом 10
                            [service_item.id] => 91168
                            [service_item.name] => L2 VPN_MINCOTSZ_MO
                            [service.id] => 91162
                            [service.name] => L2
                            [link.bw] => 2
                        )

                    [29] => stdClass Object
                        (
                            [equipment] => 95521
                            [device] => 96752
                            [link] => 95522
                            [code#1] => Array
                                (
                                    [0] => 0.500
                                    [1] => 86272.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 5504.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Дмитровское управление социальной защиты населения
                            [city.name] => Дмитров город
                            [street.name] => Профессиональная улица
                            [house.name] => дом 3А
                            [service_item.id] => 91168
                            [service_item.name] => L2 VPN_MINCOTSZ_MO
                            [service.id] => 91162
                            [service.name] => L2
                            [link.bw] => 2
                        )

                    [30] => stdClass Object
                        (
                            [equipment] => 95253
                            [device] => 96753
                            [link] => 95254
                            [code#1] => Array
                                (
                                    [0] => 0.176
                                    [1] => 213376.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Лыткаринское управление социальной защиты населения
                            [city.name] => Лыткарино город
                            [street.name] => Квартал 3А
                            [house.name] => дом 9
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 10
                        )

                    [31] => stdClass Object
                        (
                            [equipment] => 95251
                            [device] => 96752
                            [link] => 95252
                            [code#1] => Array
                                (
                                    [0] => 0.176
                                    [1] => 213376.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Отдел социальной защиты населения п Котельники
                            [city.name] => Котельники город
                            [street.name] => Ковровый микрорайон
                            [house.name] => дом 9
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 5
                        )

                    [32] => stdClass Object
                        (
                            [equipment] => 95009
                            [device] => 96752
                            [link] => 95010
                            [code#1] => Array
                                (
                                    [0] => 0.510
                                    [1] => 126976.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Отдел социальной защиты населения г Пущино
                            [city.name] => Пущино город
                            [street.name] => "Г" микрорайон
                            [house.name] => дом 13
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 10
                        )

                    [33] => stdClass Object
                        (
                            [equipment] => 94927
                            [device] => 96752
                            [link] => 94928
                            [code#1] => Array
                                (
                                    [0] => 0.510
                                    [1] => 126976.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 9472.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Орехово-Зуевское районное управление социальной защиты населения
                            [city.name] => Орехово-Зуево город
                            [street.name] => Стаханова улица
                            [house.name] => дом 24
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 20
                        )

                    [34] => stdClass Object
                        (
                            [equipment] => 94919
                            [device] => 96752
                            [link] => 94920
                            [code#1] => Array
                                (
                                    [0] => 0.510
                                    [1] => 126976.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 1152.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Орехово-Зуевское районное управление социальной защиты населения
                            [city.name] => Орехово-Зуево город
                            [street.name] => Пушкина улица
                            [house.name] => дом 7
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 20
                        )

                    [35] => stdClass Object
                        (
                            [equipment] => 95011
                            [device] => 96753
                            [link] => 95012
                            [code#1] => Array
                                (
                                    [0] => 0.510
                                    [1] => 126976.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Протвинское управление социальной защиты населения
                            [city.name] => Протвино город
                            [street.name] => Ленина улица
                            [house.name] => дом 5
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 10
                        )

                    [36] => stdClass Object
                        (
                            [equipment] => 95061
                            [device] => 96752
                            [link] => 95062
                            [code#1] => Array
                                (
                                    [0] => 0.500
                                    [1] => 86272.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 3840.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Щелковское управление социальной защиты населения Министерства социальной защиты населения Московской области
                            [city.name] => Щелково город
                            [street.name] => Краснознаменская улица
                            [house.name] => дом 12
                            [service_item.id] => 91168
                            [service_item.name] => L2 VPN_MINCOTSZ_MO
                            [service.id] => 91162
                            [service.name] => L2
                            [link.bw] => 2
                        )

                    [37] => stdClass Object
                        (
                            [equipment] => 95187
                            [device] => 96753
                            [link] => 95188
                            [code#1] => Array
                                (
                                    [0] => 0.510
                                    [1] => 126976.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 23168.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Отдел социальной защиты населения г Краснознаменск
                            [city.name] => Краснознаменск город
                            [street.name] => Генерала Шлыкова улица
                            [house.name] => дом 1
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 10
                        )

                    [38] => stdClass Object
                        (
                            [equipment] => 95079
                            [device] => 96753
                            [link] => 95080
                            [code#1] => Array
                                (
                                    [0] => 0.510
                                    [1] => 126976.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 3456.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Щелковское управление социальной защиты населения Министерства социальной защиты населения Московской области
                            [city.name] => Щелково город
                            [street.name] => Краснознаменская улица
                            [house.name] => дом 12
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 10
                        )

                    [39] => stdClass Object
                        (
                            [equipment] => 95075
                            [device] => 96753
                            [link] => 95076
                            [code#1] => Array
                                (
                                    [0] => 0.512
                                    [1] => 126208.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 2560.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Щелковское управление социальной защиты населения Министерства социальной защиты населения Московской области
                            [city.name] => Щелково город
                            [street.name] => Свирская улица
                            [house.name] => дом 14
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 10
                        )

                    [40] => stdClass Object
                        (
                            [equipment] => 94321
                            [device] => 96752
                            [link] => 94322
                            [code#1] => Array
                                (
                                    [0] => 0.508
                                    [1] => 127360.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 26496.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Воскресенское управление социальной защиты населения 
                            [city.name] => Воскресенск город
                            [street.name] => Победы улица
                            [house.name] => дом 28
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 20
                        )

                    [41] => stdClass Object
                        (
                            [equipment] => 94309
                            [device] => 96752
                            [link] => 94310
                            [code#1] => Array
                                (
                                    [0] => 0.508
                                    [1] => 127360.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 2560.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Дубненское управление социальной защиты населения
                            [city.name] => Дубна город
                            [street.name] => Вокзальная улица
                            [house.name] => дом 11А
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 20
                        )

                    [42] => stdClass Object
                        (
                            [equipment] => 93629
                            [device] => 96752
                            [link] => 93630
                            [code#1] => Array
                                (
                                    [0] => 0.508
                                    [1] => 127360.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 2304.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Дзержинское управление социальной защиты населения
                            [city.name] => Дзержинский город
                            [street.name] => Лермонтова улица
                            [house.name] => дом 7А
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 10
                        )

                    [43] => stdClass Object
                        (
                            [equipment] => 93613
                            [device] => 96752
                            [link] => 93614
                            [code#1] => Array
                                (
                                    [0] => 0.175
                                    [1] => 213760.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Шаховское управление социальной защиты населения
                            [city.name] => Шаховская поселок
                            [street.name] => 1-ая Советская улица
                            [house.name] => дом 25
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 10
                        )

                    [44] => stdClass Object
                        (
                            [equipment] => 93593
                            [device] => 96752
                            [link] => 93594
                            [code#1] => Array
                                (
                                    [0] => 0.509
                                    [1] => 126976.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 7680.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Ленинское управление социальной защиты населения
                            [city.name] => Видное город
                            [street.name] => Школьная улица
                            [house.name] => дом 60
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 20
                        )

                    [45] => stdClass Object
                        (
                            [equipment] => 93563
                            [device] => 96752
                            [link] => 93564
                            [code#1] => Array
                                (
                                    [0] => 0.508
                                    [1] => 127360.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 4864.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Клинское управление социальной защиты населения
                            [city.name] => Клин город
                            [street.name] => Карла Маркса улица
                            [house.name] => дом 18/20
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 20
                        )

                    [46] => stdClass Object
                        (
                            [equipment] => 93637
                            [device] => 96752
                            [link] => 93638
                            [code#1] => Array
                                (
                                    [0] => 0.506
                                    [1] => 127872.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Звенигородское управление социальной защиты населения
                            [city.name] => Звенигород город
                            [street.name] => Маяковского квартал
                            [house.name] => дом 9, корпус 3
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 10
                        )

                    [47] => stdClass Object
                        (
                            [equipment] => 93653
                            [device] => 96752
                            [link] => 93654
                            [code#1] => Array
                                (
                                    [0] => 0.000
                                    [1] => 172672.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Химкинское Управление социальной защиты населения
                            [city.name] => Химки город
                            [street.name] => Кирова улица
                            [house.name] => дом 16/10
                            [service_item.id] => 91168
                            [service_item.name] => L2 VPN_MINCOTSZ_MO
                            [service.id] => 91162
                            [service.name] => L2
                            [link.bw] => 2
                        )

                    [48] => stdClass Object
                        (
                            [equipment] => 93779
                            [device] => 96752
                            [link] => 93780
                            [code#1] => Array
                                (
                                    [0] => 0.175
                                    [1] => 213760.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Павлово-Посадское управление социальной защиты населения
                            [city.name] => Павловский Посад город
                            [street.name] => Орджоникидзе улица
                            [house.name] => дом 12
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 20
                        )

                    [49] => stdClass Object
                        (
                            [equipment] => 93765
                            [device] => 96753
                            [link] => 93766
                            [code#1] => Array
                                (
                                    [0] => 0.508
                                    [1] => 127360.000
                                )

                            [code#2] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#3] => Array
                                (
                                    [0] =>
                                    [1] => 0.000
                                )

                            [code#4] => Array
                                (
                                    [0] =>
                                    [1] => 9856.000
                                )

                            [synthetic_test] => Array
                                (
                                    [0] => 0
                                )

                            [administration.name] => Министерство социального развития Московской области
                            [region.name] => Московская область
                            [office.name] => Отдел социальной защиты населения г Бронницы
                            [city.name] => Бронницы город
                            [street.name] => Советская улица
                            [house.name] => дом 33
                            [service_item.id] => 91163
                            [service_item.name] => Internet
                            [service.id] => 91159
                            [service.name] => Internet
                            [link.bw] => 5
                        )

                )

        )

)

[Sat, 07 May 2016 14:47:52 +0300][/index.php][10.200.1.93] rrrrrrrrrrr
*/