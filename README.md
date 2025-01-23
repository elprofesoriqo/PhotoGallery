# Web Gallery Application

## Overview
A PHP-based web gallery application with user authentication, image upload, and management features.

## Features
- User Registration and Authentication
- Image Upload with Watermarking
- Thumbnail Generation
- Gallery Browsing
- Image Selection Management

## Technologies
- PHP
- MongoDB
- HTML/CSS
- Session Management

## Installation

### Prerequisites
- PHP 7.4+
- MongoDB
- Apache Web Server

### Steps
1. Clone repository
2. Configure database connection in `business.php`
3. Set up MongoDB
4. Configure `.htaccess` for URL rewriting
5. Ensure `web/images/` directory is writable

## Configuration
- Database: MongoDB
- Connection: localhost:27017
- Database Name: wai
- Collections: users, gallery

## Security Features
- Password hashing
- Session management
- MIME type validation
- File size restrictions

## Image Processing
- Supports JPEG and PNG
- Automatic watermarking
- Thumbnail generation
- Max file size: 1MB

## URL Routing
Clean, user-friendly URLs using `.htaccess`

## Project Structure
├── business.php        # Database interactions
├── controllers.php     # Request handling
├── dispatcher.php      # URL dispatching
├── routing.php         # URL to controller mapping
└── views/              # HTML templates
