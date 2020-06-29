<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v3.8.5">
    <title>Starter Template · Bootstrap</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/4.3/examples/starter-template/">

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">


    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
    <!-- Custom styles for this template -->
    <link href="newStyles.css" rel="stylesheet">
  </head>
  <body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
  <a class="navbar-brand" href="#">Navbar</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
<!-- 
  <div class="collapse navbar-collapse" id="navbarsExampleDefault">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
      </li>

      <li class="nav-item">
        <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown</a>
        <div class="dropdown-menu" aria-labelledby="dropdown01">
          <a class="dropdown-item" href="#">Action</a>
          <a class="dropdown-item" href="#">Another action</a>
          <a class="dropdown-item" href="#">Something else here</a>
        </div>
      </li>
    </ul>

  </div> -->
</nav>

<main role="main" class="container">
    <!-- Spinner -->
    <div class="spinner" ></div>

      <div class="col-md-12">
        <div class="row">
            <h1>Graphs</h1>
        </div>
        <div class="row">
            <input class="form-control mb-2" type="number" id="numberOfDevices" placeholder="Enter Number of devices"/>
            <button id="submit" role="button" class="btn btn-primary">Create</button>
        </div>
      </div>
      <hr class="my-4"/>
      <div class="col-md-12">
        <div class="main-content bg-light p-2" id="mainContent">

        </div>
      </div>
      <div id="chartContainer" class="col-md-6" style="height:200px"></div>
</main><!-- /.container -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

<script>
    $(document).ready(function(){
        $('.spinner').toggle();

        $('#submit').click(function(){
            const devices = new Array($('#numberOfDevices').val());
            createMaps(devices);
        });
    });

     function createMaps(devices = 1){
  
       renderElements(devices);
       
    }

    function renderElements(devices){
            $('#mainContent').empty();
            return new Promise((resolve,reject)=> {
              const getDevices = Array.apply(null, Array(parseInt(devices)))        
                .map( (el, index) => {
                    setTimeout(function () {
                        const currentElement =  `<h4 class="text-center text-white m-4 p-4 bg-info">Device No ${index+1}</h4>
                              <div class="row bg-light p-4">
                                  <div class="col-md-6 p-2" style="height:300px" id="temp-${index}" ></div>
                                  <div class="col-md-6 p-2" style="height:300px" id="humid-${index}"></div>
                                  <div class="col-md-6 p-2" style="height:300px" id="carbon-${index}"></div>
                                  <div class="col-md-6 p-2" style="height:300px" id="gas-${index}"></div>
                              </div>`;
                    // $('#mainContent').append(currentElement);
                    $(currentElement).hide().appendTo('#mainContent').fadeIn();
                    const toServer  = { "device_id":0, "temp": 0, "humid": 0, "carbon": 0, "gas": 0 };
                    toServer.temp   = renderGraphs(`temp-${index}`, "temperature");
                    toServer.humid  = renderGraphs(`humid-${index}`, "humidity");
                    toServer.carbon = renderGraphs(`carbon-${index}`, "carbon");
                    toServer.gas    = renderGraphs(`gas-${index}`, "gas");
                    toServer.device_id = "device-"+index;
                    // Ajax to PHP;
                    $.ajax({
                      url: 'server.php',
                      type: 'post',
                      data: { sensorValues: toServer },
                      success: function(response){
                        console.log("Success" + response)
                      },
                      error: function(err){
                        console.log("Error" + err)
                      }
                    })
                    }, 3000);
        }           
        );
        
        resolve(getDevices);
            });
       
    }

    function renderGraphs(canvasId, type) {   
            const types = {
                "temperature": { minValue: 15, maxValue: 40, lineValue: generateRandom(15,40) },
                "humidity"   : { minValue: 25, maxValue: 45, lineValue: generateRandom(25,45) },
                "carbon"     : { minValue: 300, maxValue: 700, lineValue: generateRandom(300,700) },
                "gas" : { minValue: 0, maxValue: 1, lineValue: generateRandom(0,1) }
            }     
            let today         = new Date();
            var chart    = new CanvasJS.Chart(canvasId, {
            animationEnabled: true,  
            title:{
                text: type
            },
            axisY: {
                title: type,
                minimum: types[type].minValue,
                maximum: types[type].maxValue,
                stripLines: [{
                    value: types[type].lineValue,
                    label: type 
                }]
            },
            axisX:{
              title: "Days",
          },
            data: [{
                yValueFormatString: "#,### Units",
                xValueFormatString: "YYYY",
                type: "spline",
                dataPoints: [
                    {x: 0, y: generateRandom(types[type].minValue, types[type].maxValue)},
                    {x: 1, y: generateRandom(types[type].minValue, types[type].maxValue)},
                    {x: 2, y: generateRandom(types[type].minValue, types[type].maxValue)},
                    {x: 3, y: generateRandom(types[type].minValue, types[type].maxValue)},
                    {x: 4, y: generateRandom(types[type].minValue, types[type].maxValue)},
                    {x: today.getHours(), y: generateRandom(types[type].minValue, types[type].maxValue)},
                    {x: today.getHours() + 1, y: generateRandom(types[type].minValue, types[type].maxValue)},
                    {x: today.getHours() + 2, y: generateRandom(types[type].minValue, types[type].maxValue)},
                    {x: today.getHours() + 3, y: generateRandom(types[type].minValue, types[type].maxValue)},
                    {x: today.getHours() + 4, y: generateRandom(types[type].minValue, types[type].maxValue)},
                ]
            }]
        });
        chart.render();
        return types[type].lineValue;
    }

    function generateRandom(min, max){
      return Math.floor(Math.random() * (max - min + 1)) + min
    }

    setInterval(() => {
      $('#mainContent').empty();
      let numberOfDevices = $("#numberOfDevices").val();
      if (typeof numberOfDevices == 'undefined' || numberOfDevices == '') numberOfDevices = 1;
      createMaps(numberOfDevices);
    }, (19000));
</script>

</body>
</html>
