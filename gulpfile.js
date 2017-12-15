/**
 * Created with gulp.
 * User: Kara
 * Date: 2017/10/10
 * Time: 15:11
 */

'use strict';

//定义依赖和插件
var gulp = require('gulp'),
    uglify = require('gulp-uglify'),
    //minifyHtml = require("gulp-minify-html"),
    htmlmin = require('gulp-htmlmin'),
    minifyCss = require("gulp-minify-css"),
    //SSI = require('browsersync-ssi'),
    imagemin = require('gulp-imagemin'),
    pngquant = require('imagemin-pngquant'),
    browserSync = require('browser-sync').create(),
    zip = require('gulp-zip'),
    babel = require("gulp-babel"),
    autoprefixer = require('gulp-autoprefixer'), // 自动添加CSS3浏览器前缀
    connect = require('gulp-connect');//livereload

var jsSrc = 'app/js/*.js';
var jsDist = 'dist/js';

var cssSrc = 'app/css/*.css';
var cssDist = 'dist/css';

var htmlSrc = 'app/*.html';
var htmlDist = 'dist';

var imgSrc = 'app/images/*';
var imgDist = 'dist/images';

var zipSrc = 'release/*';

//定义名为jsTask的任务
gulp.task('jsTask', function () {
    return gulp.src(jsSrc)
        .pipe(babel({  
            presets: ['es2015']  
        }))
        .pipe(uglify())
        .pipe(gulp.dest(jsDist))
        .pipe(browserSync.stream());

});

//定义名为cssTask的任务
gulp.task('cssTask', function () {
    return gulp.src(cssSrc)
        .pipe(autoprefixer({
            browsers: ['last 2 versions','last 2 Explorer versions','last 3 Safari versions','Firefox >= 20','> 5%','Android >= 4.0'],
            cascade: true, //是否美化属性值 默认：true 像这样：
            //-webkit-transform: rotate(45deg);
            //        transform: rotate(45deg);
            remove:false //是否去掉不必要的前缀 默认：true 
        }))
        .pipe(minifyCss()) //压缩css
        .pipe(gulp.dest(cssDist))
        .pipe(browserSync.stream());

});

//定义名为imageTask的任务
gulp.task('imageTask', function () {
    return gulp.src(imgSrc+'.{png,jpg,gif,ico}')
            .pipe(imagemin({
                optimizationLevel: 5, //类型：Number  默认：3  取值范围：0-7（优化等级）
                progressive: true, //类型：Boolean 默认：false 无损压缩jpg图片
                interlaced: true, //类型：Boolean 默认：false 隔行扫描gif进行渲染
                multipass: true //类型：Boolean 默认：false 多次优化svg直到完全优化
            }))
            .pipe(gulp.dest(imgDist))
            .pipe(browserSync.stream());
});

//定义htmlTask任务
gulp.task('htmlTask', function () {
    // return gulp.src(htmlSrc)
    //     //.pipe(browserify())
    //     .pipe(minifyHtml()) //压缩
    //     .pipe(gulp.dest(htmlDist))
    //     .pipe(browserSync.stream());

    var options = {
        removeComments: true,//清除HTML注释
        collapseWhitespace: true,//压缩HTML
        collapseBooleanAttributes: true,//省略布尔属性的值 <input checked="true"/> ==> <input />
        removeEmptyAttributes: true,//删除所有空格作属性值 <input id="" /> ==> <input />
        removeScriptTypeAttributes: true,//删除<script>的type="text/javascript"
        removeStyleLinkTypeAttributes: true,//删除<style>和<link>的type="text/css"
        ignoreCustomFragments: [/<%[\s\S]*?%>/, /<\?[\s\S]*?\?>/], //正则表达式数组，匹配的标签将不被处理。 (如 <?php ... ?>, {{ ... }}。
        minifyJS: true,//压缩页面JS
        minifyCSS: true//压缩页面CSS
    };

    return gulp.src(htmlSrc)
        .pipe(htmlmin(options))
        .pipe(gulp.dest(htmlDist))
        .pipe(browserSync.stream());

});

//定义zip任务
gulp.task('zipTask', function() {
    return gulp.src('dist/**/*.*')
        .pipe(zip('test.zip'))
        .pipe(gulp.dest('release'))
        .pipe(browserSync.stream());;
});

//使用默认任务启动 Browsersync，监听JS文件
gulp.task('serve', function () {

    // 从这个项目的根目录启动服务器
    browserSync.init({
        server: {
            baseDir:["./dist"]
        }
    });

    // 添加 browserSync.reload 到任务队列里
    // 所有的浏览器重载后任务完成。
    gulp.watch(jsSrc, ['jsTask']);
    gulp.watch(cssSrc, ['cssTask']);
    gulp.watch(imgSrc,['imageTask']);
    gulp.watch(htmlSrc, ['htmlTask']);
    gulp.watch(zipSrc,['zipTask']);
    gulp.watch(htmlDist).on("change",browserSync.reload);
    gulp.watch(jsDist).on("change",browserSync.reload);
    gulp.watch(cssDist).on("change",browserSync.reload);
    gulp.watch(imgDist).on("change",browserSync.reload);
});


gulp.task('default', ['jsTask','cssTask','imageTask','htmlTask','zipTask','serve']);