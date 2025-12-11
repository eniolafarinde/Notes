# Notes App

A simple, lightweight notes application built with **pure PHP** and **JSON-based storage**.
No database required — perfect for beginners or for environments where installing MySQL/XAMPP is difficult.

## Features

* Create new notes
* View saved notes
* Notes saved in a single `notes.json` file
* Runs on PHP’s built-in server (no frameworks required)

## 🛠 Requirements

* **PHP 7.4+** installed on your computer
  Check by running:

  ```bash
  php -v
  ```

No MySQL, XAMPP, or Apache required.

## ▶️ How to Run the App

### **Option 1 — PHP Built-In Server (Recommended)**

1. Download and unzip the project.
2. Open a terminal or command prompt.
3. Navigate to the project folder:

   ```bash
   cd /path/to/php_notes_app
   ```
4. Start the PHP development server:

   ```bash
   php -S localhost:8000
   ```
5. Open in your browser:

   ```
   http://localhost:8000
   ```

### **Option 2 — VS Code PHP Server Extension**

1. Open the project folder in **VS Code**
2. Install the extension **PHP Server**
3. Right–click `index.php`
   → **PHP Server: Serve Project**


## How Notes Are Stored

Notes are saved as JSON in `notes.json`:

```json
[
  {
    "text": "Example note",
    "timestamp": "2025-01-01 12:00:00"
  }
]
```

## Troubleshooting

### **PHP is not recognized**

Install PHP:

* Windows: [https://windows.php.net/download](https://windows.php.net/download)
* Mac: PHP usually already installed

Verify with:

```bash
php -v
```

### **Nothing shows up in the browser**

Make sure you started the server *inside the project folder*.

