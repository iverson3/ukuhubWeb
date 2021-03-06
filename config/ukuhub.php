<?php  

return [
	'music' => [
		'typeList' => [
			'弹唱' => '弹唱',
	        '单音' => '单音',
	        '指弹' => '指弹',
	        '合奏' => '合奏'
		],
		'tagList' => [
			'萌新' => '萌新',
	        '入门' => '入门',
	        '进阶' => '进阶',
	        '困难' => '困难',
	        '大神' => '大神'
		],
		'music_type' => [
			'尤克里里' => '尤克里里',
			'吉他'     => '吉他',
			'鼓'       => '鼓'
		],
		'level' => [
			'萌新' => '萌新',
			'入门' => '入门',
			'大佬' => '大佬'
		],
		'statusList' => [
			'on'  => ['value' => 1, 'text' => '可用', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => '禁用', 'color' => 'default']
		],
        'joinStatusList' => [
            'on'  => ['value' => 1, 'text' => '已报名', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => '已取消', 'color' => 'default']
        ],
		'statusSelect' => [
			0 => '禁用',
			1 => '啟用'
		],
        'joinStatus' => [
            0 => '已取消',
            1 => '已报名'
        ],
		'activityStatusList' => [
			0 => '結束',
	        1 => '開啟',
	        2 => '關閉'
		]
	]
];

?>