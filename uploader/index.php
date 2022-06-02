<div contenteditable="true" id="textarea">
    paste image here
</div>
<div class="hide" id="overlay">
    <p>アップロード中...</p>
</div>
<style>
    * {
        margin: 0;
        padding: 0;
    }

    #textarea {
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    #textarea img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    #overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    #overlay p {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
    }

    .hide {
        display: none;
    }
</style>

<script>
	const overlay = document.getElementById("overlay"), textarea = document.getElementById("textarea"),
		output = document.getElementById("output");
	document.onpaste = function (event) {
		const items = (event.clipboardData || event.originalEvent.clipboardData).items;
		let blob = null;
		for (let i = 0; i < items.length; i++) {
			if (items[i].type.indexOf("image") === 0) {
				blob = items[i].getAsFile();
				break;
			}
		}
		if (blob !== null) {
			const reader = new FileReader();
			reader.onload = function () {
				overlay.classList.toggle("hide", false);
				const upload = async () => {
					const formData = new FormData();
					const fileReq = await fetch(this.result);
					const fileRes = await fileReq.blob();
					formData.append('image', new File([fileRes], "test.png"), "upload_image");
					const req = await fetch("./image_upload.php", {method: "POST", body: formData});
					const res = await req.json();
					textarea.innerHTML = "";
					if (res.status === "success") {
						if (navigator.clipboard) {
							navigator.clipboard.writeText(`https://xpadev.net/image.php?q=${res.id}`);
						}
						textarea.innerText = `resized: https://xpadev.net/image.php?q=${res.id}\nsource: https://xpadev.net/image.php?q=${res.id}&raw=true`;
					}
					overlay.classList.toggle("hide", true);

				}
				upload();
			};
			reader.readAsDataURL(blob);
		}
	}

</script>
