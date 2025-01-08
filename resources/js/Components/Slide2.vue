<script setup>
import { ref, onMounted, watch, reactive, } from 'vue';
import * as THREE from 'three';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader';
import { RGBELoader } from 'three/examples/jsm/loaders/RGBELoader';
import axios from 'axios';

const emit = defineEmits(['saveModel','modelScreenshot']);
const canvasRef = ref(null);
const props = defineProps({
  model: {
    type: String,
    default: '',
  },
  generatedImage: {
    type: String,
    default: '',
  },
});
const emitSaveState = () => {
    emit('saveModel', modelData);
};
const modelData = reactive ({
    name: `${props.model}`, // Name of the design/model
    url:  `/storage/models/${props.model}/scene.glb`, // This can be your model data, like a base64-encoded string or a URL to a .glb or .obj file
    logos: [], // Logos or textures attached to the model, which could be either URL or base64
});
onMounted(() => {
  const canvas = canvasRef.value;
  const width = canvas.clientWidth;
  const height = canvas.clientHeight;

  // Create a Three.js scene
  const scene = new THREE.Scene();

  // Create a perspective camera
  const camera = new THREE.PerspectiveCamera(75, width / height, 0.1, 100);
  camera.position.set(0, 0, 50);

  // Create a WebGL renderer and set its size
  const renderer = new THREE.WebGLRenderer({ canvas, alpha: true });
  renderer.setSize(width, height);
  renderer.setPixelRatio(window.devicePixelRatio);
  renderer.setClearColor(0x000000, 0);

  // Add Orbit Controls
  const controls = new OrbitControls(camera, renderer.domElement);
  controls.enableDamping = true;

  // Add Ambient Light
  const ambientLight = new THREE.AmbientLight(0xffffff, 0.2);
  scene.add(ambientLight);

  // Load HDR environment map
  const rgbeLoader = new RGBELoader();
  rgbeLoader.load(
    'https://3d-cloth.tarunthummar.com/wp-content/uploads/2024/07/symmetrical_garden_02_2k.hdr',
    (texture) => {
      texture.mapping = THREE.EquirectangularReflectionMapping;
      scene.environment = texture;
    }
  );

  // Raycaster and mouse setup
  const raycaster = new THREE.Raycaster();
  const mouse = new THREE.Vector2();
  
  let otherMeshes = [];
  let activeLogo = null;
  let isDragging = false;
  let isScaling = false;
  let isRotating = false;

  // Load the GLB model
  
  const loader = new GLTFLoader();
  loader.load(
    `/storage/models/${props.model}/scene.glb`,
    (gltf) => {
 
      const model = gltf.scene;
      const box = new THREE.Box3().setFromObject(model);
      const center = box.getCenter(new THREE.Vector3());
      const size = box.getSize(new THREE.Vector3());
      model.position.sub(center); // Center the model first
      if(props.model = "mug"){ model.scale.set(150, 150, 150);}
      if(props.model = "cap"){ model.scale.set(150, 150, 150);}
      scene.add(model);

      const frontMesh = model.getObjectByName('front');
    
      if (frontMesh) {
        const frontMeshClone = frontMesh.clone();
        frontMeshClone.name = 'front_clone';
        frontMeshClone.position.copy(frontMesh.position);
        frontMeshClone.rotation.copy(frontMesh.rotation);
        frontMeshClone.scale.copy(frontMesh.scale);
        scene.add(frontMeshClone);
        
        model.traverse((child) => {
          if (child.isMesh) otherMeshes.push(child);
        });
        otherMeshes.push(frontMeshClone);

        handleTexture(frontMesh, otherMeshes);
      }

      camera.position.z = 70;
      animate();
    },
    undefined,
    (error) => {
      console.error(error);
    }
  );

  function captureScreenshot() {
    renderer.render(scene, camera);
    const screenshot = renderer.domElement.toDataURL('image/png');
    emit('modelScreenshot', screenshot);
}

  // Handle texture logic
  function handleTexture(frontMesh, mesh) {
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    const combinedTexture = new THREE.CanvasTexture(canvas);
    const textureLoader = new THREE.TextureLoader(); // Ensure textureLoader is defined
    let uvBounds = { minX: 0, minY: 0, maxX: 1, maxY: 1 }; // Default UV bounds
    let logos = []; // Initialize logos array
    let activeLogo = null;
    let isDragging = false;
    let isScaling = false;
    let isRotating = false;
    let scalingStartSize = 0;
    let scalingStartX = 0;
    let scalingStartY = 0;
    let rotationStartX = 0;
    let rotationStartY = 0;
    // Check if faceVertexUvs is available and has data
    if (frontMesh.geometry.faceVertexUvs && frontMesh.geometry.faceVertexUvs[0]) {
        const uvsArray = frontMesh.geometry.faceVertexUvs[0];
        if (uvsArray.length > 0) {
            uvsArray.forEach(uvs => {
                uvs.forEach(uv => {
                    if (uv) { // Ensure uv is not undefined
                        uvBounds.minX = Math.min(uvBounds.minX, uv.x);
                        uvBounds.minY = Math.min(uvBounds.minY, uv.y);
                        uvBounds.maxX = Math.max(uvBounds.maxX, uv.x);
                        uvBounds.maxY = Math.max(uvBounds.maxY, uv.y);
                    }
                });
            });
        }
    }
    const uvWidth = uvBounds.maxX - uvBounds.minX;
    const uvHeight = uvBounds.maxY - uvBounds.minY;
    // Set canvas dimensions based on UV dimensions or default size
    canvas.width = uvWidth * 1024; // Default size for testing
    canvas.height = uvHeight * 1024; // Default size for testing
    // Create a new material with the combined texture
    const material = new THREE.MeshBasicMaterial({ map: combinedTexture, transparent: true });
    frontMesh.material = material;
    frontMesh.material.transparent = true; // Ensure the material is transparent
    frontMesh.flipY = false; // Set the flipY property
    frontMesh.flipX = false; // Set the flipX property
    const scaleIcon = new Image();
    scaleIcon.src = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMiIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHdpZHRoPSI1MTIiIGhlaWdodD0iNTEyIj48dGl0bGU+dXAtcmlnaHQtYW5kLWRvd24tbGVmdC1mcm9tLWNlbnRlci1zb2xpZC1zdmc8L3RpdGxlPjxzdHlsZT4gLnMwIHsgZmlsbDogI2ZmZmZmZiB9IC5zMSB7IGZpbGw6ICMwMDAwMDAgfSA8L3N0eWxlPjxwYXRoIGlkPSJTaGFwZSAxIiBjbGFzcz0iczAiIGQ9Im0yNTYuNSA1MTJjLTE0MS4zIDAtMjU1LjUtMTE0LjQtMjU1LjUtMjU2IDAtMTQxLjYgMTE0LjItMjU2IDI1NS41LTI1NiAxNDEuMyAwIDI1NS41IDExNC40IDI1NS41IDI1NiAwIDE0MS42LTExNC4yIDI1Ni0yNTUuNSAyNTZ6Ij48L3BhdGg+PHBhdGggaWQ9IkxheWVyIiBjbGFzcz0iczEiIGQ9Im0zMDMuOSAxMTcuOGg3Ny45YzcuMiAwIDEzIDUuNyAxMyAxMi45djc3LjljMCA1LjItMy4yIDEwLTggMTItNC45IDItMTAuNSAwLjktMTQuMi0yLjlsLTIxLjEtMjEtNDcgNDdjLTUuMSA1LjEtMTMuMyA1LjEtMTguMyAwbC0xNy4zLTE3LjNjLTUuMS01LjEtNS4xLTEzLjMgMC0xOC4zbDQ3LTQ3LjEtMjEuMS0yMS4xYy0zLjgtMy43LTQuOS05LjMtMi45LTE0LjIgMi00LjggNi44LTcuOSAxMi03Ljl6bS05NS4xIDI3Ni43aC03Ny44Yy03LjIgMC0xMy01LjgtMTMtMTN2LTc3LjhjMC01LjMgMy4xLTEwIDgtMTIgNC45LTIgMTAuNC0wLjkgMTQuMiAyLjhsMjEgMjEuMSA0Ny4xLTQ3YzUtNS4xIDEzLjMtNS4xIDE4LjMgMGwxNy4zIDE3LjNjNS4xIDUgNS4xIDEzLjMgMCAxOC4zbC00NyA0NyAyMSAyMS4xYzMuOCAzLjcgNC45IDkuMyAyLjkgMTQuMS0yIDQuOS02LjggOC0xMiA4eiI+PC9wYXRoPjwvc3ZnPg==';
    const rotateIcon = new Image();
    rotateIcon.src = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMiIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHdpZHRoPSI1MTIiIGhlaWdodD0iNTEyIj48dGl0bGU+dXAtcmlnaHQtYW5kLWRvd24tbGVmdC1mcm9tLWNlbnRlci1zb2xpZC1zdmc8L3RpdGxlPjxzdHlsZT4gLnMwIHsgZmlsbDogI2ZmZmZmZiB9IC5zMSB7IGZpbGw6ICMwMDAwMDAgfSA8L3N0eWxlPjxwYXRoIGlkPSJTaGFwZSAxIiBjbGFzcz0iczAiIGQ9Im0yNTYuNSA1MTJjLTE0MS4zIDAtMjU1LjUtMTE0LjQtMjU1LjUtMjU2IDAtMTQxLjYgMTE0LjItMjU2IDI1NS41LTI1NiAxNDEuMyAwIDI1NS41IDExNC40IDI1NS41IDI1NiAwIDE0MS42LTExNC4yIDI1Ni0yNTUuNSAyNTZ6Ij48L3BhdGg+PHBhdGggaWQ9IkxheWVyIiBjbGFzcz0iczEiIGQ9Im0zNzUuNiAyMzcuNmg1LjFjNy45IDAgMTQuMy02LjQgMTQuMy0xNC4zdi03Ni40YzAtNS44LTMuNS0xMS4xLTguOC0xMy4zLTUuNC0yLjItMTEuNS0xLTE1LjcgMy4xbC0yNC44IDI0LjljLTUyLjMtNTEuNy0xMzYuNS01MS41LTE4OC41IDAuNi01Mi4zIDUyLjItNTIuMyAxMzYuOCAwIDE4OSA1Mi4yIDUyLjMgMTM2LjkgNTIuMyAxODkuMSAwIDcuNS03LjQgNy41LTE5LjUgMC0yNy03LjUtNy41LTE5LjYtNy41LTI3IDAtMzcuNCAzNy4zLTk3LjggMzcuMy0xMzUuMSAwLTM3LjQtMzcuMy0zNy40LTk3LjggMC0xMzUuMSAzNy4xLTM3LjEgOTcuMS0zNy4zIDEzNC41LTAuNmwtMjQuNiAyNC42Yy00LjEgNC4xLTUuMyAxMC4zLTMuMSAxNS43IDIuMiA1LjMgNy41IDguOCAxMy4zIDguOHoiPjwvcGF0aD48L3N2Zz4=';
    const deleteIcon = new Image();
    deleteIcon.src = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMiIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHdpZHRoPSI1MTIiIGhlaWdodD0iNTEyIj48dGl0bGU+dXAtcmlnaHQtYW5kLWRvd24tbGVmdC1mcm9tLWNlbnRlci1zb2xpZC1zdmc8L3RpdGxlPjxzdHlsZT4gLnMwIHsgZmlsbDogI2ZmZmZmZiB9IC5zMSB7IGZpbGw6ICMwMDAwMDAgfSA8L3N0eWxlPjxwYXRoIGlkPSJTaGFwZSAxIiBjbGFzcz0iczAiIGQ9Im0yNTYuNSA1MTJjLTE0MS4zIDAtMjU1LjUtMTE0LjQtMjU1LjUtMjU2IDAtMTQxLjYgMTE0LjItMjU2IDI1NS41LTI1NiAxNDEuMyAwIDI1NS41IDExNC40IDI1NS41IDI1NiAwIDE0MS42LTExNC4yIDI1Ni0yNTUuNSAyNTZ6Ij48L3BhdGg+PHBhdGggaWQ9IkxheWVyIiBjbGFzcz0iczEiIGQ9Im0yMDEuNiAxMDlsLTQuNSA4LjhoLTU5LjNjLTExIDAtMTkuOCA4LjktMTkuOCAxOS44IDAgMTEgOC44IDE5LjggMTkuOCAxOS44aDIzNy40YzExIDAgMTkuOC04LjggMTkuOC0xOS44IDAtMTAuOS04LjgtMTkuOC0xOS44LTE5LjhoLTU5LjNsLTQuNS04LjhjLTMuMy02LjgtMTAuMi0xMS0xNy43LTExaC03NC40Yy03LjUgMC0xNC40IDQuMi0xNy43IDExem0xNzMuNiA2OC4zaC0yMzcuNGwxMy4xIDIwOS44YzEgMTUuNyAxNCAyNy45IDI5LjYgMjcuOWgxNTJjMTUuNiAwIDI4LjYtMTIuMiAyOS42LTI3Ljl6Ij48L3BhdGg+PC9zdmc+';
    function drawCanvas() {
        context.clearRect(0, 0, canvas.width, canvas.height);
        context.fillStyle = 'transparent'; // Canvas background transparent
        context.fillRect(0, 0, canvas.width, canvas.height);
        logos.forEach(logo => {
            context.save();
            // Translate to logo center
            context.translate(logo.position.x + logo.size.width / 2, logo.position.y + logo.size.height / 2);
            // Flip on the X-axis and rotate by 180 degrees
            context.scale(-1, 1);
            context.rotate(-logo.rotation);
            // Draw the logo
            context.drawImage(logo.texture.image, -logo.size.width / 2, -logo.size.height / 2, logo.size.width, logo.size.height);
            context.restore();
        });
        if (activeLogo) {
            const iconSize = 20; // Size of the scale and rotate icons
            const iconMargin = 20; // Margin from the edge of the logo
            // Save current canvas state
            context.save();
            // Translate to logo center
            context.translate(activeLogo.position.x + activeLogo.size.width / 2, activeLogo.position.y + activeLogo.size.height / 2);
            // Apply rotation to icons
            context.rotate(activeLogo.rotation);
            // Draw the scale icon at the top-right corner of the active logo
            context.drawImage(scaleIcon, activeLogo.size.width / 2, // x
            -activeLogo.size.height / 2 - iconSize, // y
            iconSize, iconSize);
            // Draw the scale icon at the bottom-left corner of the active logo
            context.drawImage(scaleIcon, -activeLogo.size.width / 2 - iconSize, // x
            activeLogo.size.height / 2, // y
            iconSize, iconSize);
            // Draw the rotate icon at the top-left corner of the active logo
            context.drawImage(rotateIcon, -activeLogo.size.width / 2 - iconSize, // x
            -activeLogo.size.height / 2 - iconSize, // y
            iconSize, iconSize);
            // Draw the delete icon at the top-left corner of the active logo
            context.drawImage(deleteIcon, activeLogo.size.width / 2, // x
            activeLogo.size.height / 2, // y
            iconSize, iconSize);
            // Restore canvas state
            context.restore();
        }
        combinedTexture.needsUpdate = true;
        captureScreenshot();
    }
    drawCanvas();
    if (frontMesh.name == 'front') {
        document.getElementById('logo_selection').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img_url = e.target.result;
                    textureLoader.load(img_url, function (logoTexture) {
                        logoTexture.wrapS = THREE.RepeatWrapping;
                        logoTexture.wrapT = THREE.RepeatWrapping;
                        const logoWidth = 100; // Set the logo width to 100px
                        const aspectRatio = logoTexture.image.height / logoTexture.image.width;
                        const logoHeight = logoWidth * aspectRatio; // Adjust height proportionally
                        let newLogo = {
                            texture: logoTexture,
                            position:{x: 0, y: 0},
                            size: { width: logoWidth, height: logoHeight },
                            rotation: 3.164
                        }
                        if(props.model === "mug"){
                            newLogo.position = {x: 50, y:80}
                            newLogo.rotation = 0;
                        }else if(props.model === "tshirt"){
                            newLogo.position = {x: 500, y:500}
                        };
                        modelData.logos.push({
                        texture: img_url, // Store the texture reference or URL
                        position: newLogo.position,
                        size: newLogo.size,
                        rotation: newLogo.rotation
                        });
                        emitSaveState();
                        logos.push(newLogo);
                        drawCanvas();
                        captureScreenshot();
                    });
                };
                reader.readAsDataURL(file);
            }
        });
        if (props.generatedImage) {
            // Use the base64 image directly for loading into texture
            const logoTexture = new THREE.TextureLoader().load(props.generatedImage, function () {
                logoTexture.wrapS = THREE.RepeatWrapping;
                logoTexture.wrapT = THREE.RepeatWrapping;

                const logoWidth = 100; // Set the logo width to 100px
                const aspectRatio = logoTexture.image.height / logoTexture.image.width;
                const logoHeight = logoWidth * aspectRatio; // Adjust height proportionally

                let newLogo = {
                    texture: logoTexture,
                    position: { x: 0, y: 0 },
                    size: { width: logoWidth, height: logoHeight },
                    rotation: 3.164
                };

                if (props.model === "mug") {
                    newLogo.position = { x: 50, y: 80 };
                    newLogo.rotation = 0;
                } else if (props.model === "tshirt") {
                    newLogo.position = { x: 500, y: 500 };
                }

                modelData.logos.push({
                    texture: props.generatedImage, // Store the texture reference or URL
                    position: newLogo.position,
                    size: newLogo.size,
                    rotation: newLogo.rotation
                });

                emitSaveState();
                logos.push(newLogo);
                drawCanvas();
                captureScreenshot();
            });
        }
    }
    const loadSavedModel = async (itemId) => {
    try {
        const response = await axios.get(`/api/items/${itemId}`);
        const savedData = response.data;
        
        // Reconstruct the model data
        modelData.name = savedData.name;
        modelData.model = savedData.model;
        modelData.logos = savedData.logos;
        
        // Redraw the canvas with saved logos
        logos = savedData.logos;
        drawCanvas();
        
    } catch (error) {
        console.error('Error loading saved model:', error);
    }
};
    // Function to handle mouse move events
    function onMouseMove(event) {
        if (isScaling && activeLogo) {
            event.preventDefault();
            const rect = renderer.domElement.getBoundingClientRect();
            mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
            mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;
            raycaster.setFromCamera(mouse, camera);
            const intersects = raycaster.intersectObject(frontMesh);
            if (intersects.length > 0) {
                const intersect = intersects[0];
                const uv = intersect.uv;
                const x = uv.x * canvas.width;
                const y = (1 - uv.y) * canvas.height;
                const centerX = activeLogo.position.x + activeLogo.size.width / 2;
                const centerY = activeLogo.position.y + activeLogo.size.height / 2;
                // Calculate distance from the center to the mouse position
                const distance = Math.sqrt((x - centerX) ** 2 + (y - centerY) ** 2);
                // Calculate scale factor
                const scaleFactor = distance / scalingStartSize;
                // Update the size of the logo
                const newWidth = (scalingStartSize + 35) * scaleFactor;
                const newHeight = newWidth * (activeLogo.size.height / activeLogo.size.width);
                // Update the size and adjust the position
                activeLogo.size.width = newWidth;
                activeLogo.size.height = newHeight;
                activeLogo.position.x = centerX - newWidth / 2;
                activeLogo.position.y = centerY - newHeight / 2;
                drawCanvas();
            }
        }
        else if (isRotating && activeLogo) {
            event.preventDefault();
            const rect = renderer.domElement.getBoundingClientRect();
            mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
            mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;
            raycaster.setFromCamera(mouse, camera);
            const intersects = raycaster.intersectObject(frontMesh);
            if (intersects.length > 0) {
                const intersect = intersects[0];
                const uv = intersect.uv;
                const x = uv.x * canvas.width;
                const y = (1 - uv.y) * canvas.height;
                const centerX = activeLogo.position.x + activeLogo.size.width / 2;
                const centerY = activeLogo.position.y + activeLogo.size.height / 2;
                const angle = Math.atan2(y - centerY, x - centerX) - Math.atan2(rotationStartY - centerY, rotationStartX - centerX);
                activeLogo.rotation += angle;
                rotationStartX = x;
                rotationStartY = y;
                drawCanvas();
            }
        }
        else if (isDragging && activeLogo) {
            event.preventDefault();
            const rect = renderer.domElement.getBoundingClientRect();
            mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
            mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;
            raycaster.setFromCamera(mouse, camera);
            const intersects = raycaster.intersectObject(frontMesh);
            if (intersects.length > 0) {
                const intersect = intersects[0];
                const uv = intersect.uv;
                const x = uv.x * canvas.width;
                const y = (1 - uv.y) * canvas.height;
                // Update position
                activeLogo.position.x = x - activeLogo.size.width / 2;
                activeLogo.position.y = y - activeLogo.size.height / 2;
                drawCanvas();
            }
        }
    }
    // Function to handle mouse down events
    function onMouseDown(event) {
        const rect = renderer.domElement.getBoundingClientRect();
        mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;
        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObjects(mesh);
        if (intersects.length > 0) {
            const intersect = intersects[0];
            const intersectedObject = intersects[0].object;
            if (intersectedObject == frontMesh) {
                const uv = intersect.uv;
                const x = uv.x * canvas.width;
                const y = (1 - uv.y) * canvas.height;
                // Check if any logo is selected
                const selectedLogo = logos.find(logo => x >= logo.position.x &&
                    x <= logo.position.x + logo.size.width &&
                    y >= logo.position.y &&
                    y <= logo.position.y + logo.size.height);
                if (selectedLogo) {
                    // // Set the selected logo as active
                    activeLogo = selectedLogo;
                    isDragging = true;
                    controls.enabled = false;
                    drawCanvas(); // Redraw the canvas with the updated active logo
                }
                else if (isClickOnScaleIcon(x, y)) {
                    isScaling = true;
                    scalingStartSize = activeLogo.size.width;
                    const intersect = intersects[0];
                    const uv = intersect.uv;
                    scalingStartX = uv.x * canvas.width;
                    scalingStartY = (1 - uv.y) * canvas.height;
                    isDragging = true;
                    controls.enabled = false;
                }
                else if (isClickOnRotateIcon(x, y)) {
                    isRotating = true;
                    const intersect = intersects[0];
                    const uv = intersect.uv;
                    rotationStartX = uv.x * canvas.width;
                    rotationStartY = (1 - uv.y) * canvas.height;
                    isDragging = true;
                    controls.enabled = false;
                }
                else if (isClickOnDeleteIcon(x, y)) {
                    deleteLogo();
                    controls.enabled = false;
                }
                else {
                    // If clicked outside of any logo, do nothing
                    isDragging = false;
                    activeLogo = null;
                    drawCanvas(); // Redraw the canvas to remove the active state
                }
            }
            else {
                // If clicked outside of the frontMesh, do nothing
                isDragging = false;
                activeLogo = null;
                drawCanvas(); // Redraw the canvas to remove the active state
            }
        }
        else {
            // If clicked outside of the frontMesh, do nothing
            isDragging = false;
            activeLogo = null;
            drawCanvas(); // Redraw the canvas to remove the active state
        }
    }
    function isMeshVisible(mesh, camera) {
        const frustum = new THREE.Frustum();
        const cameraViewProjectionMatrix = new THREE.Matrix4();
        camera.updateMatrixWorld();
        camera.matrixWorldInverse.getInverse(camera.matrixWorld);
        cameraViewProjectionMatrix.multiplyMatrices(camera.projectionMatrix, camera.matrixWorldInverse);
        frustum.setFromProjectionMatrix(cameraViewProjectionMatrix);
        return frustum.intersectsObject(mesh);
    }
    function isClickOnScaleIcon(x, y) {
        if (activeLogo) {
            const iconSize = 20; // Size of the icon
            const iconMargin = 20; // Margin from the edge of the logo
            // Calculate icon bounds for bottom-right
            const bottomRightIconX = activeLogo.size.width / 2;
            const bottomRightIconY = -activeLogo.size.height / 2 - iconSize;
            const bottomRightIconBounds = {
                x: bottomRightIconX,
                y: bottomRightIconY,
                width: iconSize,
                height: iconSize
            };
            // Calculate icon bounds for top-left
            const topLeftIconX = -activeLogo.size.width / 2 - iconSize;
            const topLeftIconY = activeLogo.size.height / 2;
            const topLeftIconBounds = {
                x: topLeftIconX,
                y: topLeftIconY,
                width: iconSize,
                height: iconSize
            };
            // Transform the mouse coordinates into the logo's local coordinate system
            const relativeX = x - (activeLogo.position.x + activeLogo.size.width / 2);
            const relativeY = y - (activeLogo.position.y + activeLogo.size.height / 2);
            // Inverse the rotation to get local coordinates
            const angle = -activeLogo.rotation;
            const rotatedX = relativeX * Math.cos(angle) - relativeY * Math.sin(angle);
            const rotatedY = relativeX * Math.sin(angle) + relativeY * Math.cos(angle);
            // Check if the mouse click is within the bounds of either icon
            const isBottomRightIconClicked = (rotatedX >= bottomRightIconBounds.x &&
                rotatedX <= bottomRightIconBounds.x + bottomRightIconBounds.width &&
                rotatedY >= bottomRightIconBounds.y &&
                rotatedY <= bottomRightIconBounds.y + bottomRightIconBounds.height);
            const isTopLeftIconClicked = (rotatedX >= topLeftIconBounds.x &&
                rotatedX <= topLeftIconBounds.x + topLeftIconBounds.width &&
                rotatedY >= topLeftIconBounds.y &&
                rotatedY <= topLeftIconBounds.y + topLeftIconBounds.height);
            return isBottomRightIconClicked || isTopLeftIconClicked;
        }
        return false;
    }
    function isClickOnRotateIcon(x, y) {
        if (activeLogo) {
            const iconSize = 20; // Size of the rotate icon
            const iconMargin = 20; // Margin from the edge of the logo
            // Calculate the rotate icon's position relative to the active logo
            const iconX = -activeLogo.size.width / 2 - iconSize;
            const iconY = -activeLogo.size.height / 2 - iconSize;
            // Calculate the top-left and bottom-right corners of the rotate icon
            const iconBounds = {
                x: iconX,
                y: iconY,
                width: iconSize,
                height: iconSize
            };
            // Transform the mouse coordinates into the logo's local coordinate system
            const relativeX = x - (activeLogo.position.x + activeLogo.size.width / 2);
            const relativeY = y - (activeLogo.position.y + activeLogo.size.height / 2);
            // Inverse the rotation to get local coordinates
            const angle = -activeLogo.rotation;
            const rotatedX = relativeX * Math.cos(angle) - relativeY * Math.sin(angle);
            const rotatedY = relativeX * Math.sin(angle) + relativeY * Math.cos(angle);
            // Check if the mouse click is within the bounds of the rotate icon
            return (rotatedX >= iconBounds.x &&
                rotatedX <= iconBounds.x + iconBounds.width &&
                rotatedY >= iconBounds.y &&
                rotatedY <= iconBounds.y + iconBounds.height);
        }
        return false;
    }
    function isClickOnDeleteIcon(x, y) {
        if (activeLogo) {
            const iconSize = 20; // Size of the rotate icon
            const iconMargin = 20; // Margin from the edge of the logo
            // Calculate the rotate icon's position relative to the active logo
            const iconX = activeLogo.size.width / 2;
            const iconY = activeLogo.size.height / 2;
            // Calculate the top-left and bottom-right corners of the rotate icon
            const iconBounds = {
                x: iconX,
                y: iconY,
                width: iconSize,
                height: iconSize
            };
            // Transform the mouse coordinates into the logo's local coordinate system
            const relativeX = x - (activeLogo.position.x + activeLogo.size.width / 2);
            const relativeY = y - (activeLogo.position.y + activeLogo.size.height / 2);
            // Inverse the rotation to get local coordinates
            const angle = -activeLogo.rotation;
            const rotatedX = relativeX * Math.cos(angle) - relativeY * Math.sin(angle);
            const rotatedY = relativeX * Math.sin(angle) + relativeY * Math.cos(angle);
            // Check if the mouse click is within the bounds of the rotate icon
            return (rotatedX >= iconBounds.x &&
                rotatedX <= iconBounds.x + iconBounds.width &&
                rotatedY >= iconBounds.y &&
                rotatedY <= iconBounds.y + iconBounds.height);
        }
        return false;
    }
    function deleteLogo() {
        if (activeLogo) {
            const index = logos.indexOf(activeLogo);
            if (index > -1) {
                logos.splice(index, 1);
                modelData.logos.splice(index, 1);
                activeLogo = null;
                emitSaveState();
                captureScreenshot();
                drawCanvas();
            }
        }
    }
    // Function to handle mouse up events
    function onMouseUp(event) {
        if (activeLogo) {
        const index = logos.indexOf(activeLogo);
        if (index > -1) {
            modelData.logos[index] = {
                texture: activeLogo.texture.image.src,
                position: activeLogo.position,
                size: activeLogo.size,
                rotation: activeLogo.rotation
            };
            emitSaveState(); // Add emit here
        }
    }
        isDragging = false;
        isScaling = false; // Stop scaling when mouse is released
        isRotating = false; // Stop scaling when mouse is released
        controls.enabled = true;
    }
    renderer.domElement.addEventListener('mousemove', onMouseMove);
    renderer.domElement.addEventListener('mousedown', onMouseDown);
    renderer.domElement.addEventListener('mouseup', onMouseUp);
}

  // Animation loop
  function animate() {
    requestAnimationFrame(animate);
    controls.update();
    renderer.render(scene, camera);
  }

  // Handle window resizing
  const handleResize = () => {
    const width = window.innerWidth;
    const height = window.innerHeight;
    renderer.setSize(width, height);
    camera.aspect = width / height;
    camera.updateProjectionMatrix();
  };

  window.addEventListener('resize', handleResize);
  


function saveLogos(data) {
    console.log(data)
    axios.post('/api/items', data, {
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}` // Include the auth token (if using API authentication)
        }
    })
    .then(response => {
        console.log('Model data saved successfully:', response.data);
    })
    .catch(error => {
        console.error('Error saving model data:', error);
    });
}
});


</script>

<template>
    <div> 
        <input type="file" id="logo_selection" placeholder="please select logo">
        <canvas ref="canvasRef" id="reversible_side_1"></canvas>
    </div>
 
</template>

<style>
canvas {
  display: block;
  width: 100%;
  height: 100%;
}
</style>
