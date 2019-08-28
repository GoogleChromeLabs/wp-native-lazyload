/**
 * Webpack config.
 *
 * Native Lazyload, Copyright 2019 Google LLC
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

const mode = process.env.NODE_ENV === 'production' ? 'production' : 'development';

module.exports = {
	mode,
	entry: {
		lazyload: './assets/js/src/lazyload.js',
	},
	output: {
		filename: '[name].js',
		path: __dirname + '/assets/js',
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: [
					{
						loader: 'babel-loader',
						query: {
							presets: [
								'@babel/env',
								'@wordpress/default',
							],
						},
					},
					{
						loader: 'eslint-loader',
						options: {
							failOnError: true,
							formatter: require( 'eslint' ).CLIEngine.getFormatter( 'stylish' ),
						},
					},
				],
			},
		],
	},
};
