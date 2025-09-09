import { resolve } from 'path';
import { defineConfig } from 'vite';

export default defineConfig({
	build: {
		outDir: 'public',
		lib: {
			entry: resolve(__dirname, 'vite.import.js'),
			name: 'Ccrud',
			fileName: 'ccrud',
		},
		rollupOptions: {
			// Lista TODAS as peerDependencies para não incluí-las no build
			external: [
				'jquery',
				'bootstrap',
				'alertifyjs',
				'ckeditor4',
				'cropperjs',
				'jquery-mask-plugin',
				'jquery-ui-dist',
				'select2',
				'@fortawesome/fontawesome-free'
			],
			output: {
				// Fornece variáveis globais para o build UMD
				globals: {
					jquery: '$',
					bootstrap: 'bootstrap',
					alertifyjs: 'alertify',
					ckeditor4: 'CKEDITOR',
					cropperjs: 'Cropper'
					// jQuery plugins (select2, mask, ui) anexam-se a 'jquery' e não precisam de um global próprio
				},
				assetFileNames: 'ccrud.css'
			},
		},
	},
});