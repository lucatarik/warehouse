<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
   <link rel="stylesheet" type="text/css" href="./css/bootstrap.css">
   <link rel="stylesheet" type="text/css" href="./css/jquery.bootgrid.css">
   <link rel="stylesheet" type="text/css" href="./css/custom-theme/jquery-ui-1.10.3.custom.css">
   <link rel="stylesheet" type="text/css" href="./css/custom-theme/jquery-ui-1.10.3.theme.css">
   <link rel="stylesheet" type="text/css" href="./css/combobox.css">
   <script src="./js/jquery-1.12.4.min.js"></script>
   <script src="./js/bootstrap.js"></script>
   <script src="./js/jquery-ui.min.js"></script>
   <style>
       [data-column-id="qty"],[data-column-id="id"]{
           width: 6%;
       }
       [data-column-id="commands"]{
           width: 55px;
       }
   </style>
</head>

<body>
    
   <table id="grid" data-ajax="true" data-url="./rest/index.php/item" class="table table-condensed table-hover table-striped">
      <thead>
         <tr>
            <th data-column-id="id" data-type="numeric" data-order="desc">ID</th>
            <th data-column-id="name" data-formatter="fname">Nome</th>
            <th data-column-id="description">Descrizione</th>
            <th data-column-id="location_id" data-formatter="location">Dove</th>
            <th data-column-id="category_id" data-formatter="category">Categoria</th>
            <th data-column-id="qty" data-type="numeric">Quanti</th>
            <th data-column-id="commands" data-formatter="commands" data-sortable="false">Commands</th>

         </tr>
      </thead>
   </table>
<div class="">
    <form  enctype="multipart/form-data" class="addItem" method="POST" action="./rest/index.php/item">
         <div class="form-group">
    <label for="itemName">Nome</label>
    <input required="true" name="name" type="text" class="form-control" id="itemName" placeholder="Nome">
  </div>
  <div class="form-group">
    <label for="itemDesc">Descrizione</label>
    <textarea required name="description" id="itemDesc" class="form-control" rows="3"  placeholder="Descrizione"></textarea>
  </div>
  <div class="form-group">
    <input required style="    opacity: 0;    position: absolute;width: 1%;" name="itemPhoto" type="file" id="itemPhoto" accept="image/*" capture="camera">
    <p onclick="javascript:document.getElementById('itemPhoto').click()" class="help-block"><button class="btn btn-default" type="button" ><span class="icon glyphicon glyphicon-camera"> </span> Scatta foto</button></p>
  </div>
  <div class="form-group">
    <label for="itemCategory">Categoria</label>
    <select required name="category_id" id="itemCategory" class="form-control select-category"></select>
  </div>
  <div class="form-group">
    <label for="itemLocation">Posizione</label>
    <select required name="location_id" id="itemLocation" class="form-control select-location"></select>
  </div>
  <button type="submit" class="btn btn-default">Invia</button>
        
    </form>
</div>

<script src="./js/jquery.bootgrid.js"></script>
<script src="./js/combobox.js"></script>

<script>
    imgurl = './uploads/';
    apiurl = './rest/index.php/';
$(document).ready(function()
{
   var grid = $("#grid").bootgrid({
     rowCount:[5,10, 25, 50, -1],
    ajaxSettings: {
        method: "GET",
        cache: false
    },formatters: {
        "commands": function(column, row)
        {
            return "<button type=\"button\" class=\"btn btn-xs btn-default command-edit\" data-row-id=\"" + row.id + "\"><span class=\"glyphicon glyphicon-edit\"></span></button> " +
                "<button type=\"button\" class=\"btn btn-xs btn-default command-delete\" data-row-id=\"" + row.id + "\"><span class=\"glyphicon glyphicon-trash\"></span></button>";
        },
        fname:function(c,r)
         {
            var ret = "";
            if(r.photo.length)
            {
               ret = "<img src='./uploads/"+r.photo[0].fname+"_sm.jpg'> ";
            }
            return ret + r.name;
         },
        location:function(c,r)
         {
            var ret = "";
            try
            {
               ret = "<img src='"+imgurl+r.location.photo[0].fname+"_sm.jpg'> "+r.location.name;
            }catch(e){ret += r.location.name};
            return ret;
         },
        category:function(c,r)
         {
            var ret = "";
            try
            {
               if(r.category.color==null)r.category.color="#ddd";
               ret = "<div style='border-radius:20px;background-color:"+r.category.color+";display:inline;'><span style='padding:8px;color:"+r.category.color+";filter: invert(100%);    font-weight: bold;mix-blend-mode: luminosity;'>"+r.category.name+"</span></div>";
            }catch(e){};
            return ret;
         }
    },responseHandler : function (response) 
    {
        return response; 
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

loadCategory().on("edit",function()
{
    $('<form><input required placeholder="nome">').dialog({autoOpen:true,buttons: [
    {
      text: "Ok",
      icons: {
        primary: "ui-icon-save"
      },
      click: function() {
        var frm = $( this );
        var req = frm.find('[required]');
        if(req.val().length)
        {
            $.post(apiurl+"category",{name:req.val()},function(data)
            {
                if (typeof data.error == "undefined")
                {
                    loadCategory();
                    frm.dialog( "close" );
                }
                else
                {
                    frm.attr("title", "Errore nella richiesta")
            .tooltip({ close: function( event, ui ) {$( this).tooltip( "destroy" );} })
                    .tooltip("open");
                }
            },'json');
        }
        else
        {
            req.attr("title", "Seleziona un valore valido")
            .tooltip({ close: function( event, ui ) {$( this).tooltip( "destroy" );} })
                    .tooltip("open");
        }
      }
    }
  ]});
});
loadLocation().on("edit",function()
{
    $('<form><input required placeholder="nome">').dialog({autoOpen:true,buttons: [
    {
      text: "Ok",
      icons: {
        primary: "ui-icon-save"
      },
      click: function() {
        var frm = $( this );
        var req = frm.find('[required]');
        if(req.val().length)
        {
            $.post(apiurl+"location",{name:req.val()},function(data)
            {
                if (typeof data.error == "undefined")
                {
                    loadLocation();
                    frm.dialog( "close" );
                }
                else
                {
                    frm.attr("title", "Errore nella richiesta")
            .tooltip({ close: function( event, ui ) {$( this).tooltip( "destroy" );} })
                    .tooltip("open");
                }
            },'json');
        }
        else
        {
            req.attr("title", "Seleziona un valore valido")
            .tooltip({ close: function( event, ui ) {$( this).tooltip( "destroy" );} })
                    .tooltip("open");
        }
      }
    }
  ]});
});;
});


function loadCategory()
{
    var el = $('.select-category');
    $.getJSON("./rest/index.php/category",function(data) {
        el.html($('<option>').html("Seleziona categoria").val(""));
        $(data).each(function(i,v)
        {
            $('<option>').html(v.name).val(v.id).css("background-color",v.color).appendTo(el);
        });
        el.combobox();
  });
  return el;
}
function loadLocation()
{
    var el = $('.select-location');
    $.getJSON("./rest/index.php/location",function(data) {
        el.html($('<option>').html("Seleziona ubicazione").val(""));
        $(data).each(function(i,v)
        {
            var opt = $('<option>').html(v.name).val(v.id).appendTo(el);
            try{opt.css("background-image",'url('+imgurl+v.photo[0].fname+')')}catch(e){};
        });
         el.combobox();
  });
  return el;
}
</script>
</body>

</html>