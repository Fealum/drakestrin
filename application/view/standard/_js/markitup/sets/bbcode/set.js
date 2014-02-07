// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// BBCode tags example
// http://en.wikipedia.org/wiki/Bbcode
// ----------------------------------------------------------------------------
// Feel free to add more tags
// ----------------------------------------------------------------------------
mySettings = {
	previewParserPath:	'', // path to your BBCode parser
	markupSet: [
		{name:'Fett', key:'B', openWith:'[b]', closeWith:'[/b]'},
		{name:'Kursiv', key:'I', openWith:'[i]', closeWith:'[/i]'},
		{name:'Unterstrichen', key:'U', openWith:'[u]', closeWith:'[/u]'},
		{separator:'---------------' },
		{name:'Bild', key:'B', replaceWith:'[img][![Link zum Bild]!][/img]'},
		{name:'Link', key:'L', openWith:'[url=[![URL]!]]', closeWith:'[/url]', placeHolder:'Linktext'},
		{separator:'---------------' },
		{name:'Gr&ouml;&szlig;e', key:'S', openWith:'[size=[![Textgr&ouml;&szlig;e]!]]', closeWith:'[/size]',
		dropMenu :[
			{name:'Gro&szlig;', openWith:'[size=4]', closeWith:'[/size]' },
			{name:'Normal', openWith:'[size=2]', closeWith:'[/size]' },
			{name:'Klein', openWith:'[size=1]', closeWith:'[/size]' }
		]},
		{name:'Farben', className:'palette', 
		dropMenu: [
				{name:'Gelb',	openWith:'[color=#FCE94F]', 	closeWith:'[/color]', 	className:"col1-1" },
				{name:'Gelb',	openWith:'[color=#EDD400]', 	closeWith:'[/color]', 	className:"col1-2" },
				{name:'Gelb', openWith:'[color=#C4A000]', 	closeWith:'[/color]', 	className:"col1-3" },
				
				{name:'Orange', openWith:'[color=#FCAF3E]', 	closeWith:'[/color]', 	className:"col2-1" },
				{name:'Orange', openWith:'[color=#F57900]', 	closeWith:'[/color]', 	className:"col2-2" },
				{name:'Orange', openWith:'[color=#CE5C00]', 	closeWith:'[/color]', 	className:"col2-3" },
				
				{name:'Braun', 	openWith:'[color=#E9B96E]', 	closeWith:'[/color]', 	className:"col3-1" },
				{name:'Braun', 	openWith:'[color=#C17D11]', 	closeWith:'[/color]', 	className:"col3-2" },
				{name:'Braun',	openWith:'[color=#8F5902]', 	closeWith:'[/color]', 	className:"col3-3" },
				
				{name:'Gr&uuml;n', 	openWith:'[color=#8AE234]', 	closeWith:'[/color]', 	className:"col4-1" },
				{name:'Gr&uuml;n', 	openWith:'[color=#73D216]', 	closeWith:'[/color]', 	className:"col4-2" },
				{name:'Gr&uuml;n',	openWith:'[color=#4E9A06]', 	closeWith:'[/color]', 	className:"col4-3" },
				
				{name:'Blau', 	openWith:'[color=#729FCF]', 	closeWith:'[/color]', 	className:"col5-1" },
				{name:'Blau', 	openWith:'[color=#3465A4]', 	closeWith:'[/color]', 	className:"col5-2" },
				{name:'Blau',	openWith:'[color=#204A87]', 	closeWith:'[/color]', 	className:"col5-3" },
	
				{name:'Violett', openWith:'[color=#AD7FA8]', 	closeWith:'[/color]', 	className:"col6-1" },
				{name:'Violett', openWith:'[color=#75507B]', 	closeWith:'[/color]', 	className:"col6-2" },
				{name:'Violett',	openWith:'[color=#5C3566]', 	closeWith:'[/color]', 	className:"col6-3" },
				
				{name:'Rot', 	openWith:'[color=#EF2929]', 	closeWith:'[/color]', 	className:"col7-1" },
				{name:'Rot', 	openWith:'[color=#CC0000]', 	closeWith:'[/color]', 	className:"col7-2" },
				{name:'Rot',	openWith:'[color=#A40000]', 	closeWith:'[/color]', 	className:"col7-3" },
				
				{name:'Wei&szlig;', 	openWith:'[color=#FFFFFF]', 	closeWith:'[/color]', 	className:"col8-1" },
				{name:'Grau', 	openWith:'[color=#D3D7CF]', 	closeWith:'[/color]', 	className:"col8-2" },
				{name:'Grau',	openWith:'[color=#BABDB6]', 	closeWith:'[/color]', 	className:"col8-3" },
				
				{name:'Grau', 	openWith:'[color=#888A85]', 	closeWith:'[/color]', 	className:"col9-1" },
				{name:'Grau', 	openWith:'[color=#555753]', 	closeWith:'[/color]', 	className:"col9-2" },
				{name:'Schwarz',	openWith:'[color=#000000]', 	closeWith:'[/color]', 	className:"col9-3" }
			]
		},
		{separator:'---------------' },
		{name:'Ungeordnete Liste', openWith:'[list]\n', closeWith:'\n[/list]'},
		{name:'Geordnete Liste', openWith:'[list=[![Art der Liste (1: Nummerierte Liste, a: Alphabetische Liste)]!]]\n', closeWith:'\n[/list]'}, 
		{name:'Listenpunkt', openWith:'[*] '},
		{separator:'---------------' },
		{name:'Zitat', openWith:'[quote]', closeWith:'[/quote]'},
		{name:'Code', openWith:'[code]', closeWith:'[/code]'}, 
		{separator:'---------------' },
		{name:'Formatierung entfernen', className:"clean", replaceWith:function(markitup) { return markitup.selection.replace(/\[(.*?)\]/g, "") } }
	]
}