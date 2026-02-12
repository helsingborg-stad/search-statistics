import { createViteConfig } from "vite-config-factory";

const entries = {
	"js/search-enhancer": "./source/js/search-enhancer.js",
	"css/search-enhancer": "./source/sass/search-enhancer.scss",
};

export default createViteConfig(entries, {
	outDir: "assets/dist",
	manifestFile: "manifest.json",
});
