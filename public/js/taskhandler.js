$( document ).ready(function() {
    function secondsTimeSpanToHMS(s) {
        var h = Math.floor(s/3600); //Get whole hours
        s -= h*3600;
        var m = Math.floor(s/60); //Get remaining minutes
        s -= m*60;
        return h+":"+(m < 10 ? '0'+m : m)+":"+(s < 10 ? '0'+s : s); 
    }

    var timer = new easytimer.Timer();
    var startTime = endTime = totalTime = 0;

    //On timer start
    $('#timer .startButton').click(function () {
        if ($('input#task-name').val().length != 0)
        {  
            $('.required-message').html('');
            timer.start();
            startTime = new Date();

            $(".startButton").css("display", "none");
            $(".stopButton").css("display", "block");
            $("input#task-name").attr("disabled", "disabled"); 
            
        } else {
            $('.required-message').html('Please, set a name for the task.');
        } 
        
    });


    //On timer stop
    $('#timer .stopButton').click(function () {
        timer.stop();

        var storeTask = $('input#task-name').val();
        var time = $('.values').html();
                
        endTime = new Date();
        totalTime = endTime - startTime;
        totalTime /= 1000; totalTime = parseInt(totalTime);

        var foundTask = $('.task-table td.name').filter(function() {
            return $(this).text() == storeTask;
        });        

        if (foundTask.length != 0 ) {
            var timeSpan = foundTask.closest('td').next();
            seconds = parseInt(totalTime) + parseInt(timeSpan.attr('seconds'));
            timeSpan.attr('seconds', seconds);
            timeSpan.html(secondsTimeSpanToHMS(seconds));
        }
        else {
            $('.task-table tr:last').after('<tr><td class= "name">' + storeTask +'</td><td seconds =' + totalTime +'>' + time + '</td></tr>');
            $('.no-result').remove();
        }

        startTime = startTime.getTime();
        endTime = endTime.getTime();
        

        $('.task-table td.name')
        that = $(this);
        $.ajax({
            url:'/new',
            type: "POST",
            dataType: "json",
            data: {
                "task": {
                    "name": storeTask,
                    "startTime": startTime,
                    "endTime" : endTime
                }
            },
            async: true,
            success: function (data)
            {
                console.log(data)
                $('div#ajax-results').html(data.output);

            }
        });
        
        //Ajax send taskName, startDate, endDate
        
        $(".startButton").css("display", "block");
        $(".stopButton").css("display", "none");            
        $("input#task-name").removeAttr("disabled"); 

        $('.values').html('00:00:00');
        $('input#task-name').val('');
        
    });

    timer.addEventListener('secondsUpdated', function (e) {
        $('#timer .values').html(timer.getTimeValues().toString());
    });

    timer.addEventListener('started', function (e) {
        $('#timer .values').html(timer.getTimeValues().toString());
    });

    timer.addEventListener('reset', function (e) {
        $('#timer .values').html(timer.getTimeValues().toString());

    });

});