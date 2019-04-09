<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Filtered Reports - Moodle</title>
  <link rel="icon" href="./assets/favicon.ico" />
  <!-- Bootstrap core CSS -->
  <link href="bootstrap.min.css" rel="stylesheet">

  <style>
  table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
  }

  td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
  }

  tr:nth-child(even) {
    background-color: #dddddd;
  }
  </style>


  <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
  <script type="text/javascript">
      $(document).ready(function(){
      $('form input[type="date"]').prop("disabled", true);
      $(".agree").click(function(){
              if($(this).prop("checked") == true){
                  $('form input[type="date"]').prop("disabled", false);
              }
              else if($(this).prop("checked") == false){
                  $('form input[type="date"]').prop("disabled", true);
              }
          });
      });
  </script>





</head>