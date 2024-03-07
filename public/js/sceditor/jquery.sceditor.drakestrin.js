$(document).ready(function() {
    $.sceditor.command.set("handlung", {
		exec: function () {
			this.wysiwygEditorInsertHtml('<span class="action post">', '</span>');
		},
		txtExec: ['[h]', '[/h]'],
		tooltip: 'Handlung',
		shortcut: 'ctrl+h'
    });
    
	$.sceditor.plugins.bbcode.bbcode.set('h', {
		tags: {
			span: null
		},
	    format: '[h]{0}[/h]',
	    html: '<span class="action post">{0}</span>'
	});
	
    $.sceditor.command.set("simoff", {
    	forceNewLineAfter: ['s'],
		exec: function () {
			this.wysiwygEditorInsertHtml('<s class="simoff post">', '</s>');
		},
		txtExec: ['[s]', '[/s]'],
		tooltip: 'SimOff',
		shortcut: 'ctrl+m'
    });
    
	$.sceditor.plugins.bbcode.bbcode.set('s', {
		tags: {
			s: null
		},
		isInline: false,
	    format: '[s]{0}[/s]',
	    html: '<s class="simoff post">{0}</s>'
	});
	
	$(".textarea-bbcode").sceditor({
		plugins: "bbcode",
		toolbar: "bold,italic|bulletlist,orderedlist|handlung,simoff,quote,code|image,link,unlink|emoticon|removeformat,maximize,source",
		colors: '#FCE94F,#EDD400,#C4A000,#FCAF3E,#F57900,#CE5C00|#E9B96E,#C17D11,#8F5902,#8AE234,#73D216,#4E9A06|#729FCF,#3465A4,#204A87,#AD7FA8,#75507B,#5C3566|#EF2929,#CC0000,#A40000,#EF299E,#CC0078,#A40060|#FFFFFF,#D3D7CF,#BABDB6,#888A85,#555753,#000000',
		emoticonsRoot: configurl + "/img/emoticon/",
		emoticons: {
			// Emoticons to be included in the dropdown
			dropdown: {
				":)": "30.gif",
				":))": "31.gif",
				":]": "32.gif",
				":D": "33.gif",
				":rofl:": "34.gif",
				";)": "35.gif",
				":(": "36.gif",
				";o": "37.gif",
				";(": "38.gif",
				"X(": "39.gif",
				"X/": "40.gif",
				":teufel:": "41.gif",
				":o": "54.gif",
				"?(": "55.gif",
				"8o": "56.gif",
				"8)": "57.gif",
				":P": "58.gif",
				":tongue:": "59.gif",
				":rolleyes:": "63.gif",
				":baby:": "64.gif",
				":hallo:": "65.gif",
				":genial:": "66.gif",
				"<3": "67.gif",
				":>": "69.gif",
				":3": "72.gif"
			},
			// Emoticons to be included in the more section
			more: {
				":adel:": "42.gif",
				":koenig:": "43.gif",
				":kaiser:": "44.gif",
				":veuxin:": "45.gif",
				":trinken": "46.gif",
				":party:": "47.gif",
				":ritter:": "48.gif",
				":angriff:": "49.gif",
				":fechter:": "50.gif",
				":spam:": "51.gif",
				":dito:": "52.gif",
				":urlaub:": "53.gif",
				":richter:": "60.gif",
				":hero:": "61.gif",
				":ritter2:": "62.gif",
				":tinos:": "68.gif",
				":met:": "70.gif"
			},
			// Emoticons that are not shown in the dropdown but will still
			// be converted. Can be used for things like aliases
			hidden: {
				"=)": "31.gif",
				":evil:": "40.gif",
				":birth:": "47.gif",
				":cool:": "57.gif",
				":verliebt:": "67.gif"
			}
		},
		style: configurl + "/js/sceditor/development/jquery.sceditor.drakestrin.css",
		resizeWidth: false,
		autoExpand: true,
		locale: "de"
		});
});