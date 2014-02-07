// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// UVNcode
// ----------------------------------------------------------------------------
mySettings = {
	previewParserPath:	'', // path to your UVNcode parser
	markupSet: [
		{name:'Kapitel', className:'chapter', openWith:'<k[![Kapitelnummer]!]>', closeWith:'</k>'},
		{	name:'Paragraph', 
			className:'paragraph', 
			openBlockWith:'<p[![Paragraphennummer]!]|[![Paragraphentitel]!]>\n', 
			openWith:'<+>', 
			closeWith:'</+>',
			closeBlockWith:'\n</p>',
			multiline: true
		},
		{	name:'Artikel', 
			className:'article', 
			openBlockWith:'<a[![Artikelnummer]!]|[![Artikeltitel]!]>\n', 
			openWith:'<+>', 
			closeWith:'</+>',
			closeBlockWith:'\n</a>',
			multiline: true
		},
		{separator:'---------------' },
		{	name:'Liste', 
			className:'list', 
			openBlockWith:'<l>\n', 
			openWith:'<+>', 
			closeWith:'</+>',
			closeBlockWith:'\n</l>',
			multiline: true
		},
		{name:'Listenpunkt', className:'list-item', openWith:'<+>', closeWith:'</+>'},
		{separator:'---------------' },
		{name:'Kommentierende Anmerkung', className:'comment', openWith:'<:>', closeWith:'</:>'},
		{name:'Hervorhebung', className:'marking', openWith:'<\!>', closeWith:'</\!>'}, 
		{name:'Absatz', className:'text', openWith:'<>', closeWith:'</>'}, 
		{separator:'---------------' },
		{name:'Formatierung entfernen', className:"clean", replaceWith:function(markitup) { return markitup.selection.replace(/<(.*?)>/g, "") } }
	]
}