var grid = document.getElementById("gif-grid");
const dontAutoPlay = true;
var msnry = new Masonry( '#gif-grid', { itemSelector: ".grid-item", columnWidth: 300 ,fitWidth: true});
function getParameterByName(name, url) {
	if (!url) {
		url = window.location.href;
	}
	name = name.replace(/[\[\]]/g, "\\$&");
	var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
		results = regex.exec(url);
	if (!results) return null;
	if (!results[2]) return '';
	return decodeURIComponent(results[2].replace(/\+/g, " ").replace(/%2F/g, "/"));
}
function hideUploadForm(){
	document.getElementById("upload-div").style.display="none";
}
function displayUploadForm(){
	document.getElementById("upload-div").style.display="block";
}
var remainingToLoad = 0;
function createItemElement(gif){
		remainingToLoad++;

		console.log(gif.video);
		var url = gif.url;
		var item = document.createElement("div");
		item.classList.add("grid-item");
		
		var img =undefined;
		if(gif.video == null){
			img	= document.createElement("img");
			if(dontAutoPlay)
				   img.setAttribute("data-gifffer",url);
			else
			   img.setAttribute("src",gif.url);
			img.onload= function(){ 
				remainingToLoad --;
				msnry.layout();
			}
		} else {
			img	= document.createElement("video");
			img.src = gif.video;
			if(!dontAutoPlay)
				img.autoplay = true;
			else {
				 var playF = function(event) {
				  if(!img.isPlaying && event.type != "mouseout") {
					img.play();
				  }
				 else if(event.type != "mouseover") {
					img.pause();
				  }
				};
				img.addEventListener('click', playF);
				img.addEventListener('mouseover', playF);
				img.addEventListener('mouseout', playF);
			}
			
			img.loop = true;
			img.onloadedmetadata = function(){ 
				remainingToLoad --;
				msnry.layout();
			}
		}
		Gifffer();
		var subT = document.createElement("div");
		subT.classList.add("sub");
		var dl = document.createElement("a");
		dl.href=url
		var dlImg = document.createElement("img");
		dlImg.src= "img/download.svg";
		dl.appendChild(dlImg);
		subT.appendChild(dl);
		item.appendChild(img);
		item.appendChild(subT);
		item.onmouseover=function(){
			this.getElementsByClassName("sub")[0].style.display="block";
		}
		item.onmouseout=function(){
			this.getElementsByClassName("sub")[0].style.display="none";
		}
		return item;
	}
	function addItemElement(url){
		var item = createItemElement(url)
		grid.appendChild(item);
		msnry.appended( item );
	}
	var getJSON = function(url, callback) {
		var xhr = new XMLHttpRequest();
		xhr.open('GET', url, true);
		xhr.responseType = 'json';
		xhr.onload = function() {
		  var status = xhr.status;
		  if (status === 200) {
			callback(null, xhr.response);
		  } else {
			callback(status, xhr.response);
		  }
		};
		xhr.send();
	};
	var current = 0;	
	var getList = function(start){
		current = start;
		var url = "get.php?start="+start;
		var query = getParameterByName("query");
		if(query != null)
			url += "&query="+query;
		getJSON(url,function(status, data){
			for(var gif of data){
					addItemElement(gif);
			}
			hasLoaded = true;
			
		});
	}
	var hasLoaded = false;
	getList(0);
	window.onscroll = function(ev) {
	    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight-200 && remainingToLoad<=5 && hasLoaded) {
			console.log(window.innerHeight + window.scrollY);
			hasLoaded = false;
			getList(current+20);
	    }
	};


