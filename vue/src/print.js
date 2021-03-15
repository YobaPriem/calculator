import pdfMake from 'pdfmake/build/pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';
pdfMake.vfs = pdfFonts.pdfMake.vfs;

const docDefinition = {
	content: [
		{text: 'Tables', style: 'header'},
		{text: 'Column/row spans', style: 'subheader'},
		'Each cell-element can set a rowSpan or colSpan',
		{
			style: 'tableExample',
			color: '#444444',
			table: {
				widths: [200, 'auto', 'auto'],
				headerRows: 1,
				// keepWithHeaderRows: 1,
				body: [
					[{
						text: 'Header with Colspan = 2',
						style: 'tableHeader',
						colSpan: 2,
						alignment: 'center',
					}, {}, {text: 'Header 3', style: 'tableHeader', alignment: 'center'}],
					[{text: 'Header 1', style: 'tableHeader', alignment: 'center'}, {
						text: 'Header 2',
						style: 'tableHeader',
						alignment: 'center',
					}, {text: 'Header 3', style: 'tableHeader', alignment: 'center'}],
					['Sample value 1', 'Sample value 2', 'Sample value 3'],
					[{
						rowSpan: 3,
						text: 'rowSpan set to 3\nLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor',
					}, 'Sample value 2', 'Sample value 3'],
					['', 'Sample value 2', 'Sample value 3'],
					['Sample value 1', 'Sample value 2', 'Sample value 3'],
					['Sample value 1', {
						colSpan: 2,
						rowSpan: 2,
						text: 'Both:\nrowSpan and colSpan\ncan be defined at the same time',
					}, ''],
					['Sample value 1', '', ''],
				],
			},
		},
	],
	styles: {
		header: {
			fontSize: 18,
			bold: true,
			margin: [0, 0, 0, 10],
		},
		subheader: {
			fontSize: 16,
			bold: true,
			margin: [0, 10, 0, 5],
		},
		tableExample: {
			margin: [0, 5, 0, 15],
		},
		tableOpacityExample: {
			margin: [0, 5, 0, 15],
			fillColor: 'blue',
			fillOpacity: 0.3,
		},
		tableHeader: {
			bold: true,
			fontSize: 13,
			color: 'black',
		},
	},
	defaultStyle: {
		// alignment: 'justify'
	},
};

export function save(materials) {
	const pdf = pdfMake.createPdf(docDefinition);
	pdf.download();
}
