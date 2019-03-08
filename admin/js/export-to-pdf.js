// date for report header
var d = new Date();
var date_today = d.getDate() + '/' + (d.getMonth() + 1) + '/' + d.getFullYear();


function generate() {

	var totalPagesExp = "{total_pages_count_string}";

	// l = landscape (use p for portrait); mm = size in millimeters
	// The 3rd parameter of the constructor can take an array of the dimensions eg new jsPDF('p', 'mm', [297, 210]);
	var doc = new jsPDF('l', 'mm', 'a1');

	doc.autoTable({

		html: '#orders_table',
		didDrawPage: function(data) {
			// header
			doc.setFontSize(20);
			doc.setTextColor(40);
			doc.setFontStyle('normal');
			// WPsettings.WPtitle and WPsettings.WPurl come from woocommerce-orders-export.php
			doc.text("Orders from " + WPsettings.WPtitle + " (" + WPsettings.WPurl + "). Report exported on: " + date_today, data.settings.margin.left + 15, 22);
			// footer
			var str = "Page " + doc.internal.getNumberOfPages();
			if (typeof doc.putTotalPages === 'function') {
				str = str + " of " + totalPagesExp;
			}
			doc.setFontSize(10);
			var pageSize = doc.internal.pageSize;
			var pageHeight = pageSize.height ? pageSize.height : pageSize.getHeight();
			doc.text(str, data.settings.margin.left, pageHeight - 10);
		},
		margin: {top: 30},
		bodyStyles: {valign: 'top'},
		styles: {cellWidth: 'wrap', rowPageBreak: 'auto', halign: 'justify'},
		columnStyles: {text: {cellWidth: 'auto'}}

	})

	// Total page number plugin only available in jspdf v1.0+
	if (typeof doc.putTotalPages === 'function') {
		doc.putTotalPages(totalPagesExp);
	}
	// Save
	doc.save("Orders from " + WPsettings.WPtitle + ".pdf");

}


function fewercols() {

	var totalPagesExp = "{total_pages_count_string}";
	var doc = new jsPDF('l','mm', 'a2');

	var head = headRows();
	var body = bodyRows();

	doc.autoTable({

		head: head,
		body: body,
		didDrawPage: function(data) {
			// header
			doc.setFontSize(20);
			doc.setTextColor(40);
			doc.setFontStyle('normal');
			doc.text("Orders from " + WPsettings.WPtitle + " (" + WPsettings.WPurl + "). Report exported on: " + date_today, data.settings.margin.left + 15, 22);
			// footer
			var str = "Page " + doc.internal.getNumberOfPages();
			if (typeof doc.putTotalPages === 'function') {
				str = str + " of " + totalPagesExp;
			}
			doc.setFontSize(10);
			var pageSize = doc.internal.pageSize;
			var pageHeight = pageSize.height ? pageSize.height : pageSize.getHeight();
			doc.text(str, data.settings.margin.left, pageHeight - 10);
		},
		margin: {top: 30}

	})

	// Total page number plugin only available in jspdf v1.0+
	if (typeof doc.putTotalPages === 'function') {
		doc.putTotalPages(totalPagesExp);
	}

	// save
	doc.save("Summary Orders from " + WPsettings.WPtitle + ".pdf");
}


function headRows() {
	return [{booking: 'Booking', order: 'Order', total: 'Total', billing: 'Billing', meta_data: 'Meta data'}];
}

function bodyRows() {

	var row = document.getElementsByClassName("table_row");

	var body = [];

	for (var i = 0; i < row.length; i++) {
		body.push({
			booking: row[i].getElementsByTagName('td')[0].textContent,
			order: row[i].getElementsByTagName('td')[1].textContent,
			total: row[i].getElementsByTagName('td')[2].textContent,
			billing: row[i].getElementsByTagName('td')[3].textContent,
			meta_data: row[i].getElementsByTagName('td')[4].textContent,
		});
	}

	return body;

}
