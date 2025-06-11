$(document).ready(function(){

	moment.locale('ja');
	var objTable = $('#newest_list');
	var obj;
	var strUnixTime;
	objTable.find('tr.row').each(function(){
		obj = $(this);
		
		//strUnixTime = new Date(obj.find('.entry_ts').text()); // Date
		var time = obj.find('.entry_ts').text();
		//time = time.replace(' ','T')+'+0900';

		obj.find('.past_status')
			.text(moment(time).fromNow());
		
		console.log(time);
	});




});

// function displayTime(unixTime) {
// 	unixTime = Date.parse(unixTime);
// 	var date = new Date(unixTime)
// 	var diff = new Date().getTime() - date.getTime()
// 	var d = new Date(diff);
// 
// 	if (d.getUTCFullYear() - 1970) {
// 		return d.getUTCFullYear() - 1970 + '年前'
// 	} else if (d.getUTCMonth()) {
// 		return d.getUTCMonth() + 'ヶ月前'
// 	} else if (d.getUTCDate() - 1) {
// 		return d.getUTCDate() - 1 + '日前'
// 	} else if (d.getUTCHours()) {
// 		return d.getUTCHours() + '時間前'
// 	} else if (d.getUTCMinutes()) {
// 		return d.getUTCMinutes() + '分前'
// 	} else {
// 		return d.getUTCSeconds() + '秒前'
// 	}
// }