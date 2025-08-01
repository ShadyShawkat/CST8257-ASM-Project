// CUSTOM SCRIPT
console.log("General Kenobi! ⚔️");

// MyAlbums.php
if (document.querySelector("#albumList")) {
  const albumList = document.querySelector("#albumList");

  // Delete buttons
  const deleteButtons = albumList.querySelectorAll("button[data-album-id");
  deleteButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      const albumId = button.getAttribute("data-album-id");
      const albumRow = button.closest("tr");

      // Get the album name
      const albumName = albumRow
        .querySelector("td")
        .querySelector("a").textContent;

      modalTitle = "Delete Album?";
      modalMessage = `Are you sure you want to delete the album "${albumName}"?<br><br><strong>NOTE :</strong> This will also delete all the pictures in the album.`;

      showModal(modalTitle, modalMessage).then((confirmed) => {
        if (!confirmed) return;

        fetch("././api/deletealbum.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: `albumId=${encodeURIComponent(albumId)}`,
        })
          // .then((response) => response.text())
          // .then((rawText) => {
          //   console.log("Raw response:", rawText);
          // })

          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              displayMessage(data.message, "Album Deleted", "success");
              albumRow.remove();
            } else {
              console.error("Delete failed:", data.error || "Unknown error");
            }
          })
          .catch((error) => console.error("AJAX error:", error));
      });
    });
  });
}

if (document.querySelector("form #albumselect")) {
  // Listener for change event when select is changed in mypictures.php
  // const pictureList = document.querySelector("form#pictureList");
  const albumSelect = document.querySelector("form #albumselect");
  albumSelect.addEventListener("change", () => {
    const pictureTitle = document.querySelector("h3#picturetitle");
    const thumbContainer = document.querySelector("#picturethumbs");
    const mainImage = document.querySelector("#mainImage");
    const pictureDescription = document.querySelector("#pictureDescription p");
    const pictureId = document.querySelector("#pictureId");

    // Clear the main image. Use placeholder by default.
    mainImage.src = "./assets/images/Placeholder.svg";

    // Clear the thumbnails container
    thumbContainer.innerHTML = "";
    pictureTitle.innerHTML = "";
    pictureDescription.textContent = "No description set.";

    // Clear the comments
    document.querySelector(".comment-list").innerHTML = "No comment found.";

    fetch("././api/getpicturelist.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `albumId=${encodeURIComponent(albumSelect.value)}`,
    })
      // .then((response) => response.text())
      // .then((rawText) => {
      //   console.log("Raw response:", rawText);
      // })

      .then((response) => response.json())
      .then((data) => {
        const userId = data.userId;

        console.log(data);

        if (data.status == "success" && Array.isArray(data.pictureList)) {
          const firstPicture = data.pictureList[0];
          const albumId = firstPicture.Album_Id;

          // Show the title
          pictureTitle.innerHTML = firstPicture.Title;

          // Show the description
          if (firstPicture.Description) {
            pictureDescription.textContent = firstPicture.Description;
          } else {
            pictureDescription.textContent = "No description set.";
          }
          // Make the first picture the main image
          const firstPicturePath = `./uploads/${data.userId}/${albumId}/${data.pictureList[0].FileName}`;
          mainImage.src = firstPicturePath;

          mainImage.dataset.id = firstPicture.Picture_Id;
          pictureId.value = firstPicture.Picture_Id;

          // Show the list of images
          const ul = document.createElement("ul");

          data.pictureList.forEach((picture) => {
            const li = document.createElement("li");

            const imgSrc = `./uploads/${userId}/${albumId}/${picture.FileName}`;
            const html = `<img class="thumbnails" id=${picture.Picture_Id} src="${imgSrc}" data-fullsize="${imgSrc}">`;

            li.innerHTML = html;
            ul.appendChild(li);
          });

          thumbContainer.appendChild(ul);

          getComments(firstPicture.Picture_Id);
        } else {
          pictureId.value = "";
          mainImage.dataset.id = "";
        }
      })
      .catch((error) => console.error("AJAX error:", error));
  });

  // Listener for clicks on the thumbnails in mypicture.php
  document.querySelector("#picturethumbs").addEventListener("click", (event) => {
      if (event.target.matches(".thumbnails")) {
        const mainImage = document.querySelector("#mainImage");
        const pictureId = document.querySelector("#pictureId");
        const thumbnails = document.querySelectorAll(".thumbnails");

        thumbnails.forEach((thumb) => {
          thumb.addEventListener("click", function () {
            const fullSrc = this.getAttribute("data-fullsize");
            // Show the image in the main image container.
            mainImage.src = fullSrc;
            mainImage.dataset.id = this.id;

            pictureId.value = this.id;

            thumbnails.forEach((i) => i.classList.remove("selected-thumbnail"));
            // Add highlight to clicked image
            this.classList.add("selected-thumbnail");

            getComments(this.id);
          });
        });
      }
    });
}

// Function to get the comments.
function getComments(pictureId) {
  // fetch the comments
  fetch("././api/getcomments.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `pictureId=${encodeURIComponent(pictureId)}`,
  })
    // .then((response) => response.text())
    // .then((rawText) => {
    //   console.log("Raw response:", rawText);
    // })

    .then((response) => response.json())
    .then((data) => {
      const commentList = document.querySelector(".comment-list");

      // Clear the comments
      commentList.innerHTML = "";

      // Load the comments
      if (data.status == "success" && Array.isArray(data.commentsList)) {
        data.commentsList.forEach((comment) => {
          const li = document.createElement("li");
          li.classList.add("mb-1");

          // Display format is Name (Date): Comment
          html = `<span class='fst-italic'>${comment.Author_Name} (${comment.Comment_Date})</span>: ${comment.Comment_Text}`;

          li.innerHTML = html;
          commentList.appendChild(li);
        });
      } else {
        html = `<li class="mb-1">${data.commentsList}</li>`;
        commentList.innerHTML = html;
      }
    })
    .catch((error) => console.error("AJAX error:", error));
}

// MyPictures Comment text area
if (document.querySelector("form#pictureComment")) {
  const commentForm = document.querySelector("form#pictureComment");

  commentForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const pictureId = document.querySelector("#mainImage").dataset.id;
    const spanStatus = document.querySelector("span#statusMsg");

    const formData = new FormData(commentForm);
    const data = Object.fromEntries(formData.entries());

    spanStatus.textContent = "";

    if (spanStatus.classList.contains("text-success")) {
      spanStatus.classList.remove("text-success");
      spanStatus.classList.add("text-danger");
    }

    if (pictureId == "") {
      spanStatus.textContent = "You cannot comment on a non-existing picture.";
    } else if (data.commentArea == "") {
      spanStatus.textContent = "Comment is blank.";
    } else {
      fetch("././api/addcomment.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `pictureId=${encodeURIComponent(pictureId)}&commentText=${encodeURIComponent(data.commentArea)}`,
      })
        // .then((response) => response.text())
        // .then((rawText) => {
        //   console.log("Raw response:", rawText);
        // });

        .then((response) => response.json())
        .then((data) => {
          if (data.status == "success") {
            spanStatus.classList.remove("text-danger");
            spanStatus.classList.add("text-success");
            spanStatus.textContent = "Comment added.";

            document.querySelector('#commentArea').value = "";

            console.log(pictureId);
            getComments(pictureId);
          } else {
            console.error(
              "Adding comment failed:",
              data.error || "Unknown error"
            );
          }
        });
    }

  });
}

// Bootstrap modal
function showModal(title, message) {
  return new Promise((resolve) => {
    const modalHtml = `
      <div class="modal" id="modalMessage" tabindex="-1" aria-labelledby="modalMessage" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header bg-danger">
              <h5 class="modal-title h5 fs-5 text-white" id="modalMessage">${title}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              ${message}
            </div>
            <div class="modal-footer">
              <button type="button" id="modalBtnConfirm" class="btn btn-primary">Yes</button>
              <button type="button" id="modalBtnCancel" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
            </div>
          </div>
        </div>
      </div>`;

    document.body.insertAdjacentHTML("beforeend", modalHtml);

    const modalEl = document.querySelector("#modalMessage");
    const myModal = new bootstrap.Modal(modalEl);

    const confirmBtn = modalEl.querySelector("#modalBtnConfirm");
    const cancelBtn = modalEl.querySelector("#modalBtnCancel");

    confirmBtn.addEventListener("click", () => {
      resolve(true);
      myModal.hide();
      modalEl.remove();
    });

    cancelBtn.addEventListener("click", () => {
      resolve(false);
      myModal.hide();
      modalEl.remove();
    });

    myModal.show();
  });
}

// function to display a message
function displayMessage(message, title = "ERROR", type = "error") {
  let alertClass = "alert-light";
  let svgIcon = "";

  // Determine Bootstrap alert class and SVG icon based on type
  switch (type.toLowerCase()) {
    case "error":
      alertClass = "alert-danger";
      svgIcon =
        '<svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M479.56-254Q507-254 526-272.56q19-18.56 19-46t-18.56-46.94q-18.56-19.5-46-19.5T434-365.71q-19 19.29-19 46.73 0 27.44 18.56 46.21t46 18.77ZM421-430h118v-263H421v263Zm59.28 368Q393-62 317.01-94.58q-75.98-32.59-132.91-89.52-56.93-56.93-89.52-132.87Q62-392.92 62-480.46t32.58-163.03q32.59-75.48 89.52-132.41 56.93-56.93 132.87-89.52Q392.92-898 480.46-898t163.03 32.58q75.48 32.59 132.41 89.52 56.93 56.93 89.52 132.64Q898-567.55 898-480.28q0 87.28-32.58 163.27-32.59 75.98-89.52 132.91-56.93 56.93-132.64 89.52Q567.55-62 480.28-62Z"/></svg>';
      break;
    case "info":
    case "success":
      alertClass = "alert-success";
      title = "SUCCESS"; // Change to success if none is provided
      break;
  }

  // Create the alert div element
  const alertDiv = document.createElement("div");
  alertDiv.className = `m-2 alert ${alertClass} alert-dismissible fade show`;
  alertDiv.setAttribute("id", "alertMessage"); // Consider making this ID dynamic if multiple alerts can be shown
  alertDiv.setAttribute("role", "alert");

  // Populate the inner HTML
  alertDiv.innerHTML = `
            ${svgIcon}
            <strong>${title} : </strong>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;

  // Get the body element
  const mainElement = document.querySelector("main");

  // Ensure the main element exists before trying to add
  if (mainElement) {
    // Check if there is an existing alert with the same ID is present and remove it
    const existingAlert = document.getElementById("alertMessage");
    if (existingAlert) {
      existingAlert.remove();
    }
    mainElement.prepend(alertDiv);
  }
}
