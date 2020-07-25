$( document ).ready(function() {

    //Turns seconds into H:M:S
    function secondsTimeSpanToHMS(s) {
        var h = Math.floor(s/3600); //Get whole hours
        s -= h*3600;
        var m = Math.floor(s/60); //Get remaining minutes
        s -= m*60;
        return h+":"+(m < 10 ? '0'+m : m)+":"+(s < 10 ? '0'+s : s); 
    }


    //update the total counter time
    function updateTotalTime() {
        $seconds = 0; 
        
        $( "td[seconds]" ).each( function( index ){
            $seconds += parseInt($( this ).attr( "seconds" ));
        });

        $('.total-value').html(secondsTimeSpanToHMS($seconds));
    }

    var timer = new easytimer.Timer();
    var startTime = endTime = totalTime = 0;
    
    window.addEventListener("load", function(){
        updateTotalTime();
    });

    //On timer start
    $('#timer .startButton').click(function () {

        //check if there's a task written
        if ($('input#task-name').val().length != 0)
        {  
            //remove require message in case it has poped up
            $('.required-message').html('');

            //start timer
            timer.start();
            //set the starting date
            startTime = new Date();

            //show stop button, hide start button and disable textbox
            $(".startButton").css("display", "none");
            $(".stopButton").css("display", "block");
            $("input#task-name").attr("disabled", "disabled"); 
            
        } else {
            $('.required-message').html('Please, set a name for the task.');
        }         
    });


    //On timer stop
    $('#timer .stopButton').click(function () {

        //stop timer
        timer.stop();

        //get the task name and the time
        var storeTask = $('input#task-name').val();
        var time = $('.values').html();
        
        //creates a date for the end Time and calculate the time it took to end
        endTime = new Date();
        totalTime = endTime - startTime;
        totalTime /= 1000; totalTime = parseInt(totalTime);

        //check if the task already exists
        var foundTask = $('.task-table td.name').filter(function() {
            return $(this).text() == storeTask;
        });        

        //if found, takes the seconds attribute, sums it up and displays again the time formatted
        if (foundTask.length != 0 ) {
            var timeSpan = foundTask.closest('td').next();
            seconds = parseInt(totalTime) + parseInt(timeSpan.attr('seconds'));
            timeSpan.attr('seconds', seconds);
            timeSpan.html(secondsTimeSpanToHMS(seconds));
        }
        else {
            //if the task doesn't exist, crate new row with the task, time and seconds attribute
            $('.task-table tr:last').after('<tr><td class= "name">' + storeTask +'</td><td seconds =' + totalTime +'>' + time + '</td></tr>');
            $('.no-result').remove();
        }

        //Update total timer
        updateTotalTime();
        startTime = startTime.getTime();
        endTime = endTime.getTime();
        
        //send ajax with the new submitted information
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
                // console.log(data)
            }
        });
        
        //sets buttons, label and time to original state
        $(".startButton").css("display", "block");
        $(".stopButton").css("display", "none");            
        $("input#task-name").removeAttr("disabled"); 

        $('.values').html('00:00:00');
        $('input#task-name').val('');
        
    });


    //add listeners for the buttons
    timer.addEventListener('secondsUpdated', function (e) {
        $('#timer .values').html(timer.getTimeValues().toString());
    });

    timer.addEventListener('started', function (e) {
        $('#timer .values').html(timer.getTimeValues().toString());
    });
    
});