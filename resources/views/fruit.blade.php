<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

{{--
These have been commented out as I've been struggling with read access to local files
For whatever reason i couldn't unrestrict the access  so i've thrown it all in the blade file
albeit making it look a lot worse than what it should be.

--}}
{{--<link rel="stylesheet" href="fruit.css">--}}
{{--<script src="fruit.js"></script>--}}

<div class="header">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>Fruits</title>
    </head>

    <input type="text" id="myInput" placeholder="Title...">
    <span onclick="newElement()" class="addBtn">Add</span>
</div>
<ul id="fruitsUl">
    @foreach($fruits as $fruit)
       <li class="{{$fruit->id}}">{{$fruit->name}}</li>
    @endforeach
</ul>


<script>
    var list = document.getElementsByTagName("LI");
    var close = document.getElementsByClassName("close");
    var nextDate = new Date();

    /**
     * Records the current time and
     */
    if (nextDate.getMinutes() === 0) {
        callEveryHour()
    } else {
        nextDate.setHours(nextDate.getHours() + 1);
        nextDate.setMinutes(0);
        nextDate.setSeconds(0);

        var difference = nextDate - new Date();
        setTimeout(callEveryHour, difference);
    }

    for (var i = 0; i < list.length; i++) {
        var span = document.createElement("SPAN");
        var txt = document.createTextNode("\u00D7");
        span.className = "close";
        span.appendChild(txt);
        list[i].appendChild(span);
    }

    for (var i = 0; i < close.length; i++) {
        close[i].onclick = function(i) {
            var div = this.parentElement;
            div.style.display = "none";
            removeFruit(this.parentElement.getAttribute('class'));
        }
    }

    // Create a new list item when clicking on the "Add" button
    function newElement(value = '') {
        var inputValue = document.getElementById("myInput").value ?? value;
        var li = document.createElement("li");
        var t = document.createTextNode(inputValue);

        if (inputValue === '') {
            alert("Please Provide Input");
        } else {
            document.getElementById("fruitsUl").appendChild(li);
        }

        li.appendChild(t);
        var span = document.createElement("SPAN");
        var txt = document.createTextNode("\u00D7");

        span.className = "close";
        span.appendChild(txt);
        li.appendChild(span);

        for (i = 0; i < close.length; i++) {
            close[i].onclick = function() {
                var div = this.parentElement;
                div.style.display = "none";
            }
        }

        // Update Backend for new value.
        updateFruitTable(inputValue);
    }
    function updateFruitTable(inputValue) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: '/updateFruitTable',
            data: {
                'name':inputValue
            },
            success: function() {
                console.log("New Fruit Added");
            }
        });
    }

    function removeFruit(fruitNumber) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: '/removeFruit',
            data: {
                'id':fruitNumber
            },
            success: function() {
                console.log("Fruit Removed");
            }
        });
    };

    /**
     * update 1 hour from current time.
     */
    function callEveryHour() {
        setInterval(UpdateFruitByJson, 1000 * 60 * 60);
    }

    // Endpoint Refuses to work from all attempts to access it
    // Leaving in skeleton code for bonus points.
    function UpdateFruitByJson() {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "GET",
            url: '/getJsonFruit',
            success: function() {
                // Reload the new data that's been saved into the table.
                reload();
            }
        });
    };

    function reload() {
        location.reload();
    }
</script>

<style>
    body {
        margin: 0;
        min-width: 250px;
    }

    * {
        box-sizing: border-box;
    }

    ul {
        margin: 0;
        padding: 0;
    }

    ul li {
        cursor: pointer;
        position: relative;
        padding: 12px 8px 12px 40px;
        list-style-type: none;
        background: #eee;
        font-size: 18px;
        transition: 0.2s;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    ul li:nth-child(odd) {
        background: #f9f9f9;
    }

    ul li:hover {
        background: #ddd;
    }

    .close {
        position: absolute;
        right: 0;
        top: 0;
        padding: 12px 16px 12px 16px;
    }

    .close:hover {
        background-color: #f44336;
        color: white;
    }

    .header {
        background-color: #f44336;
        padding: 30px 40px;
        color: white;
        text-align: center;
    }

    .header:after {
        content: "";
        display: table;
        clear: both;
    }

    input {
        margin: 0;
        border: none;
        border-radius: 0;
        width: 75%;
        padding: 10px;
        float: left;
        font-size: 16px;
    }

    .addBtn {
        padding: 10px;
        width: 25%;
        background: #d9d9d9;
        color: #555;
        float: left;
        text-align: center;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
        border-radius: 0;
    }

    .addBtn:hover {
        background-color: #bbb;
    }
</style>
