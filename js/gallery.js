var selectedYear = $("#gallery-year-select select").val();

function selectPhoto(img) {
  $("#gallery-items .list .list-item.selected").removeClass("selected");
  img.addClass("selected");
  
  $("#gallery-items .current").html(`<img class="current-image loading" data-src="gallery.php?photo_id=${img.attr("data-id")}" />`);
  $("#gallery-items .current .current-image").unveil();
}

function selectAlbum(albumButton) {
  if(!albumButton) return;
  var id = albumButton.attr("data-id");
  $(".gallery-album-select-item.selected").removeClass("selected");
  albumButton.addClass("selected");
  
  $("#gallery-items").addClass("loading");
  $("#gallery-items").removeClass("error");
  $("#gallery-items .list-inner, #gallery-items .selected").html("");
  $.get("gallery.php", {album_id: id}).done(function(data){
    $("#gallery-items").removeClass("loading");
    
    for(var photo of data.photoset.photo) {
      $("#gallery-items .list-inner").append(`<img class="list-item loading" data-id="${photo.id}" data-src="gallery.php?photo_id=${photo.id}&thumb" />`);
    }
    
    $("#gallery-items .list .list-item").click(function(){
      selectPhoto($(this));
    }).unveil();
    
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