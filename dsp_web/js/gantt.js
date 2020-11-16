function compare(a,b){
    return (a.value < b.value ? 1 : -1);
};

$(function() {
    "use strict";


    var tmenberSource = [];
    var max_date_list = [];
    // var color_list = ["Yellow"];
    var color_list = ["Red","Green","Blue","Orange","Purple","Yellow","LightBlue","lemonchiffon"];


    var count=0;

    for(let user of user_json) {
      var inout_tmp=[];
      count++;
      for (var i = 0, len = Object.keys(in_json[user]).length; i < len; i++) {
        var inout_data = {
          from:in_json[user][i],
          to:out_json[user][i],
          customClass: 'gantt' + color_list[count%color_list.length]
        }
        inout_tmp.push(inout_data);
      }
      max_date_list[user] = out_json[user][i-1];

      var addData ={
        name:user,
        values:inout_tmp,
        sort:out_json[user][i-1]
      }
      // console.log(addData);
      tmenberSource.push(addData);
    }
    console.log(tmenberSource[0].name);
    tmenberSource.sort(function(a,b){
      if(a.sort < b.sort) return 1;
      if(a.sort > b.sort) return -1;
      return 0;
    });
    delete tmenberSource['sort'];
    console.log(tmenberSource);
    //
    // const pretty = JSON.stringify(tmenberSource, null, 4)
    // console.log(pretty)

    // shifts dates closer to Date.now()
    var offset = new Date().setHours(0, 0, 0, 0) -
        new Date(tmenberSource[0].values[0].from).setDate(35);
    for (var i = 0, len = tmenberSource.length, value; i < len; i++) {
        value = tmenberSource[i].values[0];
        value.from += offset;
        value.to += offset;
    }

    $(".gantt").gantt({
        source: tmenberSource,
        dow: ["Sun","Mon","Tue","Wen","Thr","Fri","Sat"],
        navigate: "scroll",
        //scale: "days",
        maxScale: "months",
        minScale: "hours",
        itemsPerPage: 30,
        onRender: function() {
            if (window.console && typeof console.log === "function") {
                console.log("chart rendered");
            }
        }
    });

    /*$(".gantt").popover({
        selector: ".bar",
        title: function _getItemText() {
            return this.textContent;
        },
        container: '.gantt',
        content: "Here's some useful information.",
        trigger: "hover",
        placement: "auto right"
    });*/

    prettyPrint();

});


console.log(user_json);
console.log(in_json);
console.log(out_json);
