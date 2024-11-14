<template>
    <div>
        <p>Użyj narzędzi do dostosowania grafiki na modelu 3D</p>
        <canvas class="w-full" id="3dCanvas"></canvas>
        <!-- Reset Button -->
        <button @click="resetView" class="mt-4 px-4 py-2 bg-indigo-500 text-white rounded">Resetuj widok</button>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import * as THREE from 'three';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls'; // Import OrbitControls

const selectedModel = ref('mug');
let controls, camera, scene, renderer;
let initialCameraPosition, initialModelPosition, initialModelRotation, loadedModel;

onMounted(() => {
    init3DModel();
});

// Function to initialize the 3D model
const init3DModel = () => {
    const canvas = document.getElementById('3dCanvas');
    scene = new THREE.Scene();
    camera = new THREE.PerspectiveCamera(75, canvas.clientWidth / canvas.clientHeight, 0.1, 100);
    renderer = new THREE.WebGLRenderer({ canvas, antialias: true });
    renderer.setSize(canvas.clientWidth, canvas.clientHeight, false);
    renderer.outputEncoding = THREE.sRGBEncoding; // Ensure textures render correctly

    // Ambient light to brighten the scene
    const ambientLight = new THREE.AmbientLight(0xffffff, 1);
    scene.add(ambientLight);

    // Directional light to simulate sunlight
    const directionalLight = new THREE.DirectionalLight(0xffffff, 0.5);
    directionalLight.position.set(10, 10, 10);
    scene.add(directionalLight);

    // Initialize OrbitControls
    controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.25;
    controls.enableZoom = true;
    controls.enablePan = true;

    // Load the 3D model
    const loader = new GLTFLoader();
    loader.load(`/storage/models/${selectedModel.value}/scene.gltf`, (gltf) => {
        loadedModel = gltf.scene; // Store a direct reference to the loaded model

        // Calculate the bounding box to determine the height
        const box = new THREE.Box3().setFromObject(loadedModel);
        const center = box.getCenter(new THREE.Vector3());
        const size = box.getSize(new THREE.Vector3());

        // Move the model so that it starts at half its height above the ground
        loadedModel.position.sub(center); // Center the model first
        loadedModel.position.y = size.y / 2*-1; // Raise it to half its height

        loadedModel.scale.set(30, 30, 30);

        // Save initial model position and rotation
        initialModelPosition = loadedModel.position.clone();
        initialModelRotation = loadedModel.rotation.clone();

        // Add the model to the scene
        scene.add(loadedModel);

        // Set initial camera position
        camera.position.set(7, 6, 4);
        initialCameraPosition = camera.position.clone();

        animate();
    });

    const animate = () => {
        requestAnimationFrame(animate);
        controls.update();
        renderer.render(scene, camera);
    };

    window.addEventListener('resize', () => {
        camera.aspect = canvas.clientWidth / canvas.clientHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(canvas.clientWidth, canvas.clientHeight, false);
    });
};

// Function to reset the camera and model to their initial states
const resetView = () => {
    if (loadedModel) {
        // Reset camera position
        camera.position.copy(initialCameraPosition);
        camera.lookAt(0, 0, 0);

        // Reset the controls to their initial state
        controls.target.set(0, 0, 0);
        controls.update();

        // Reset model's position and rotation
        loadedModel.position.copy(initialModelPosition);
        loadedModel.rotation.copy(initialModelRotation);
    }
};
</script>

<style scoped>
#3dCanvas {
    margin: 5px;
    width: 100%;
    height: 400px;
    border: 1px solid #ccc;
}
</style>
