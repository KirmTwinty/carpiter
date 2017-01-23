var current_song_index = -1;
var current_song_title = [];
var current_song_path = [];
function play(obj){
    // Update status of the song
    previous_song_index = current_song_index;
    $(obj).addClass('playing');
    
    current_song_index = $(obj).children('.song-index').text() ;
    // Play selected file
    player = new MediaElementPlayer('#audio-player');
    player.pause();
    player.setSrc($(obj).children('.fullpath').text());
    title = $(obj).children('.title').text();
    $('.audio-player-title').text(title);
    player.play();
    // Remove previous one
    if (previous_song_index != -1 && previous_song_index != current_song_index){
	$('.song-item').each(function(index){
	    if(index == previous_song_index){
		$(this).removeClass('playing');
	    } 
	});
    }
    // Update library
    current_song_title = [];
    current_song_path = [];
    $('.song-item').each(function (){
	current_song_title.push($(this).children('.title').text());
	current_song_path.push($(this).children('.fullpath').text());
    });
    
}
function update_ui(old_index){
    // Remove old
    $('.song-item').each(function(index){
	if(index == previous_song_index){
	    $(this).removeClass('playing');
	}else if(index == current_song_index){
	    $(this).addClass('playing');
	}
    });
}
function play_next(){
    len = current_song_title.length;
    if (current_song_index != -1 && current_song_index < len - 1){
	previous_song_index = current_song_index;
	current_song_index++;
	player = new MediaElementPlayer('#audio-player');
	player.pause();
	player.setSrc(current_song_path[current_song_index]);
	$('.audio-player-title').text(current_song_title[current_song_index]);
	player.play();

	if($('.page_info').text() == 'mediapage'){
	    update_ui(previous_song_index);
	}
    }
}
function play_previous(){
    if (current_song_index > 0){
	previous_song_index = current_song_index;
	current_song_index--;
	player = new MediaElementPlayer('#audio-player');
	player.pause();
	player.setSrc(current_song_path[current_song_index]);
	$('.audio-player-title').text(current_song_title[current_song_index]);
	player.play();

	if($('.page_info').text() == 'mediapage'){
	    update_ui(previous_song_index);
	}
    }
}

function action_button(obj){
	var toLoad = obj.getAttribute("href") + ' #content';
	// var toLoad = $("#previousbtn").attr('href')+' #content'; 
	$('#content').hide('fast',loadContent);
	$('#load').remove();
	$('#wrapper').append('<span id="load">LOADING...</span>');
	$('#load').fadeIn('normal');
	
	window.location.hash = "";
	function loadContent() {
		$('#content').load(toLoad,'',showNewContent())
	}
	function showNewContent() {
		$('#content').show('normal',hideLoader());
		
	}
	function hideLoader() {
		$('#load').fadeOut('normal');
	}
	
	return false;
	
}
