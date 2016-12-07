<head>
   <link rel="stylesheet" type="text/css" href="./css/bootstrap.css">
   <link rel="stylesheet" type="text/css" href="./css/jquery.bootgrid.css">
   <link rel="stylesheet" type="text/css" href="./css/custom-theme/jquery-ui-1.10.3.custom.css">
   <link rel="stylesheet" type="text/css" href="./css/custom-theme/jquery-ui-1.10.3.theme.css">
   <script src="./js/jquery-1.12.4.min.js"></script>
   <script src="./js/bootstrap.js"></script>
   <script src="./js/jquery-ui.min.js"></script>

</head>

<body>
   <table id="grid" data-ajax="true" data-url="./rest/index.php/item" class="table table-condensed table-hover table-striped">
      <thead>
         <tr>
            <th data-column-id="id" data-type="numeric" data-order="desc">ID</th>
            <th data-column-id="name" data-formatter="fname">Name</th>
            <th data-column-id="description">description</th>
            <th data-column-id="qty" data-type="numeric">qty</th>
            <th data-column-id="commands" data-formatter="commands" data-sortable="false">Commands</th>

         </tr>
      </thead>
   </table>

</body>
<script src="./js/jquery.bootgrid.js"></script>

<script>
$(document).ready(function()
{
   var grid = $("#grid").bootgrid({
     rowCount:[3,5,10, 25, 50, -1],
    ajaxSettings: {
        method: "GET",
        cache: false
    },formatters: {
        "commands": function(column, row)
        {
            return "<button type=\"button\" class=\"btn btn-xs btn-default command-edit\" data-row-id=\"" + row.id + "\"><span class=\"fa fa-pencil\"></span></button> " +
                "<button type=\"button\" class=\"btn btn-xs btn-default command-delete\" data-row-id=\"" + row.id + "\"><span class=\"fa fa-trash-o\"></span></button>";
        },
        fname:function(c,r)
         {
            var ret = "";
            if(r.photo.length)
            {
               ret = "<img src='"+r.photo.fname+"'> ";
            }
            return ret + r.name;
         }
    }
}).on("loaded.rs.jquery.bootgrid", function()
{
    /* Executes after data is loaded and rendered */
    grid.find(".command-edit").on("click", function(e)
    {
        alert("You pressed edit on row: " + $(this).data("row-id"));
    }).end().find(".command-delete").on("click", function(e)
    {
        alert("You pressed delete on row: " + $(this).data("row-id"));
    });
});
});
</script>