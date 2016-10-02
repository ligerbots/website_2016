var selectedYear = $("#gallery-year-select select").val();
var req = null;

function selectPhoto(img) {
  $("#gallery-items .list .list-item.selected").removeClass("selected");
  img.addClass("selected");
  
  var placeholder = $(`<img class="current-image" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" />`);
  $("#gallery-items .current").html('').append(placeholder);
  
  var src = img.attr("data-large");
  
  var actual = new Image();
  
  var timeout = setTimeout(function(){
    placeholder.addClass("loading");
  }, 500);
  
  actual.classList.add("current-image");
  actual.classList.add("fade-in");
  actual.onload = function() {
    clearTimeout(timeout);
    $("#gallery-items .current").html('').append(actual);
  };
  actual.onerror = function() {
    $("#gallery-items .list-inner, #gallery-items .selected").html("");
    $("#gallery-items").addClass("error");
  };
  actual.src = src;
}

function selectAlbum(albumButton) {
  if(!albumButton) return;
  if(req) req.abort();
  
  var id = albumButton.attr("data-id");
  $(".gallery-album-select-item.selected").removeClass("selected");
  albumButton.addClass("selected");
  
  $("#gallery-items").addClass("loading");
  $("#gallery-items").removeClass("error");
  $("#gallery-items .list-inner, #gallery-items .selected").html("");
  
  req = $.get("gallery.php", {album_id: id}).done(function(data){
    console.log(data);
    $("#gallery-items").removeClass("loading");
    
    for(var photo of data.photoset.photo) {
      $("#gallery-items .list-inner").append(`<img class="list-item loading" data-large="${photo.url_m}" data-src="${photo.url_sq}" />`);
    }
    
    $("#gallery-items .list .list-item").click(function(){
      selectPhoto($(this));
    }).unveil(); // lazy-load thumbnails as necessary
    
    selectPhoto($($("#gallery-items .list .list-item").get(0)));
  }).fail(function(){
    $("#gallery-items").removeClass("loading");
    $("#gallery-items").addClass("error");
  });
}

function renderSidebar(year) {
  $("#gallery-album-select").html("");
  var albums = window.albumsByYear[year];
  albums.sort(function(a,b){return a.name.localeCompare(b.name);})
  for(var album of albums) {
    $("#gallery-album-select").append(`<button class="gallery-album-select-item" data-id="${album.id}">${album.name}</button>`);
  }
  
  $(".gallery-album-select-item").click(function(){
    selectAlbum($(this));
  });
  selectAlbum($($(".gallery-album-select-item").get(0)));
}

$(document).ready(function() {
  setTimeout(function(){
    renderSidebar(selectedYear);
  }, 1);
});

$("#gallery-year-select select").change(function(){
  selectedYear = $(this).val();
  renderSidebar(selectedYear);
});