# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Repository Information

This repository contains a PHP web application for managing a catalog gift system for a company called "Ellena". It uses a PatTemplate-based architecture for rendering pages and connects to a PostgreSQL database.

The system consists of two main parts:
- `cataloggift2507/` - The frontend web server files
- `api_cataloggift2507/` - The API backend (which connects to the database server)

## Environment Configuration

The application uses the following proxy settings:

```bash
HTTP_PROXY=http://172.26.67.100:80
HTTPS_PROXY=http://172.26.67.100:80
NO_PROXY=localhost,127.0.0.1
```

## Project Architecture

The system follows a simple MVC-like pattern:

1. Frontend (`cataloggift2507/`):
   - `include/config.php` - Main configuration settings
   - `include/config_log.php` - Log-related configuration
   - `include/model.php` - Contains the base model class with common utility functions
   - `include/templateContainer.php` - Template rendering system based on PatTemplate
   - `include/choiceList/` - CSV files containing item, shop, and prefecture data
   - CSS/JS assets for frontend rendering

2. Backend API (`api_cataloggift2507/`):
   - `include/config.php` - Database connection configuration
   - `include/classDB.php` - Database wrapper class
   - `include/modelEllenaCatalogGift2507.php` - Main model for database operations
   - API endpoints for database operations

The system is designed to:
- Display and filter catalog gifts from the database
- Show order information from the `t_ellena_cataloggift2507` table
- Map CSV data to database records

## Database Structure

The main database table is `t_ellena_cataloggift2507`, which contains:

- `entry_sid` - Sequential ID
- `entry_ts` - Timestamp of entry
- `receipt_num` - Receipt number
- `user_id` - Store ID (corresponds to shop names in CSV)
- `c_item` - Item ID (corresponds to product names in CSV)
- Other personal information fields for shipping and delivery

## Development Tasks

To create a new page that displays gift data from the database:

1. Create a new PHP file in the main directory (similar to `logviewer.php`)
2. Include the necessary configuration files and model classes
3. Create a new page class that retrieves data from the API
4. Create a corresponding template file in `include/template/`
5. Use the existing API endpoints to fetch data

The application uses `modelEllenaCatalogGift2507->execLogOutput()` to fetch data from the backend.

## Authentication

The admin pages use basic HTTP authentication with the following credentials:
- Username: `ellena`
- Password: `CatalogGift2507`

## Testing

To test the application:
1. Ensure the database connection is properly configured
2. Access the logviewer page at `cataloggift2507/logviewer.php`
3. Login using the authentication credentials