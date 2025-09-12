const autoprefixer = require("autoprefixer"),
	concat = require("gulp-concat"),
	cssnano = require("cssnano"),
	gulp = require("gulp"),
	path = require("path"),
	postcss = require("gulp-postcss"),
	sass = require("gulp-sass"),
	browserSync = require("browser-sync").create();

const postCSSOptions = [
	autoprefixer(),
	//mqpacker(), // Combine media query rules.
	cssnano(), // Minify.
];

const sassOptions = {
	includePaths: path.resolve(__dirname, "./src/"),
};

gulp.task("style-editor", function () {
	return gulp
		.src([path.resolve(__dirname, "./src/**/sass/editor.scss")])
		.pipe(sass(sassOptions).on("error", sass.logError))
		.pipe(concat("editor_blocks_custom.css"))
		.pipe(postcss(postCSSOptions))
		.pipe(gulp.dest("dist/"));
});

gulp.task("style", function () {
	return gulp
		.src([
			path.resolve(__dirname, "./src/common.scss"),
			path.resolve(__dirname, "./src/**/sass/style.scss"),
		])
		.pipe(sass(sassOptions).on("error", sass.logError))
		.pipe(concat("frontend_blocks.css"))
		.pipe(postcss(postCSSOptions))
		.pipe(gulp.dest("dist/"));
});

gulp.task("build-process", gulp.parallel("style", "style-editor"));

gulp.task("build", gulp.series("build-process"));

const watchFuncs = (basePath = ".") => {
	/**
	 * Add your host name
	 **/
	const siteName = "http://berg.local";
	/**
	 * If you want chanege port uncomment this
	 **/
	// const portNumber = 4000;

	gulp
		.watch(
			[`${basePath}/src/**/*.scss`],
			gulp.parallel(["style", "style-editor"]),
			browserSync.init({
				proxy: siteName,
				/**
				 * If you want chanege port uncomment this
				 */
				// port: portNumber,
			})
		)
		.on("change", browserSync.reload);
};

gulp.task(
	"watch",
	gulp.series("build-process", () => watchFuncs())
);
