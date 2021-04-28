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
		if (gif.thumbnail != null){
			img = document.createElement("img");
			img.src = gif.thumbnail;
			img.alt = gif.description;
			img.onmouseover = function(){
				if(gif.video != null){
				  var video = document.createElement("video");
                                  video.src = gif.video;
                                  video.muted = true;
				  video.title = gif.description;
                                  video.preload="auto"; 
                                  video.autobuffer=true;
var playF = function(event) {
                                  if(!video.isPlaying && event.type != "mouseout") {
                                        video.play();
                                  }
                                 else if(event.type != "mouseover") {
                                        video.pause();
                                  }
                                };
                                video.addEventListener('click', playF);
                                video.addEventListener('mouseover', playF);
                                video.addEventListener('mouseout', playF);
                                   video.autoplay = true;
				  video.loop = true;
                                   video.onloadedmetadata = function(){
			           img.replaceWith(video);
					}
				} else
				img.src=gif.url
			}
			img.onload= function(){ 
                                remainingToLoad --;
                                msnry.layout();
                        }

		}else if(gif.video == null){
			img	= document.createElement("img");
			if(dontAutoPlay&&false)
				   img.setAttribute("data-gifffer",url);
			else
			   img.setAttribute("src",gif.url);
			img.onload= function(){ 
				remainingToLoad --;
				msnry.layout();
			}
			img.alt = gif.description;
		} else {
			img	= document.createElement("video");
			img.src = gif.video;
			img.muted = true;
			img.preload="auto"; 
img.title = gif.description;
            img.autobuffer=true;

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
		var share = document.createElement("a");
		share.href=url
		share.onclick=function(){
			postOnMastodon(gif.video != undefined ? gif.video : url);
			return false;
		}
		var shareImg = document.createElement("img");
		shareImg.src= "img/mastodon-icon.png";
		share.appendChild(shareImg);
		subT.appendChild(share);
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
			setTimeout(function(){
				msnry.layout();
			},3000);
			
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

function getCurrentUrl(){
	return window.location.protocol+"//"+window.location.hostname+window.location.pathname;
}

var api = new MastodonAPI({
                instance: localStorage.getItem("mastodon_url"),
                api_user_token: localStorage.getItem("mastodon_token")
            });;

if (window.location.href.indexOf("?code=") !== -1) {
                // nice, we got our auth code!
                // lets put it into a variable
                var authCode = window.location.href.replace(window.location.origin + window.location.pathname + "?code=", "");
                // nice variable clusterfuck, eh?
                // we have everything needed to access our oauth token
                api.getAccessTokenFromAuthCode(
                    localStorage.getItem("mastodon_client_id"),
                    localStorage.getItem("mastodon_client_secret"),
                    localStorage.getItem("mastodon_client_redirect_uri"),
                    authCode,
                    function(data) {
						console.log(data);
						api.setConfig("api_user_token", data["access_token"])
						localStorage.setItem("mastodon_token", data["access_token"]);
                        postItem(localStorage.getItem("mastodon_to_post"))
                        localStorage.removeItem("mastodon_to_post")
                    }
                )
}
function postItem(url){
	$("#mastodon-post").show();
	document.getElementById("mastodon-post-button").onclick = function(event){
		event.preventDefault();
		$("#mastodon-post").hide();
		var xhr = new XMLHttpRequest();
		xhr.responseType = 'blob';
		xhr.onload = function() {
		var reader = new FileReader();
			reader.onloadend = function() {
			var byteCharacters = atob(reader.result.slice(reader.result.indexOf(',') + 1));
			var byteNumbers = new Array(byteCharacters.length);
			for (var i = 0; i < byteCharacters.length; i++) {
			  byteNumbers[i] = byteCharacters.charCodeAt(i);
			}
			var byteArray = new Uint8Array(byteNumbers);
			var blob = new Blob([byteArray], {type: 'video/webm'});
			var url = URL.createObjectURL(blob);
			var formData = new FormData();
			formData.append('file', blob,'test.webm');
			api.postMedia("media",
				formData
			,function(data){
					const mediaId = data.id;
					api.post("statuses", {status:$("#mastodon-post-message").val()+(document.getElementById("mastodon-post-via").checked ? " via "+window.location.protocol+"//"+window.location.hostname+window.location.pathname:""), media_ids:[mediaId]}, function (data) {
						
					});
				})
				
		  }
		  reader.readAsDataURL(xhr.response);
		};

		xhr.open('GET', url);
		xhr.send();
	};
	
	
}
function onClickAuth(url){
	localStorage.setItem("mastodon_url",url);
	api = new MastodonAPI({
                instance: url,
                api_user_token: localStorage.getItem("mastodon_token"),
            });
	api.registerApplication("OhMyGif",getCurrentUrl(), // redirect uri, we will need this later on
	["write"], //scopes
	getCurrentUrl(), //website on the login screen
	function(data) {
		// we got our application
		// lets save it to our browser storage
		localStorage.setItem("mastodon_client_id", data["client_id"]);
		localStorage.setItem("mastodon_client_secret", data["client_secret"]);
		localStorage.setItem("mastodon_client_redirect_uri", data["redirect_uri"]);
		// now, that we have saved our application data, generate an oauth url and send
		// our user to it!
		window.location.href = api.generateAuthLink(data["client_id"],
			data["redirect_uri"],
			"code", // oauth method
			["write"] //scopes
		);
	});
}

function postOnMastodon(url){
	//if not auth

	if (localStorage.getItem("mastodon_token") == undefined){
		localStorage.setItem("mastodon_to_post",url);
		$("#mastodon-auth").show();
	}else {
		postItem(url);
	}
	
}


