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
function createItemElement(url){
	
		var item = document.createElement("div");
		item.classList.add("grid-item");
		var img = document.createElement("img");
		if(dontAutoPlay)
	           img.setAttribute("data-gifffer",url);
		else
		   img.setAttribute("src",url);

		img.onload= function(){ 
			msnry.layout();
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
					addItemElement(gif['url']);
			}
			
		});
	}
	getList(0);
	window.onscroll = function(ev) {
	    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight-200) {
		getList(current+20);
	    }
	};


