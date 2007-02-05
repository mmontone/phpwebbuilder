;; Emacs Mode for PHPWebBuilder
;; Author: Mariano Montone - marianomontone@gmail.com
;; Organization: Eureka Consulting - www.eureka-consulting.com.ar
;; License: GPL
;; Requires: php-mode

;; To configure the mode, place the following in your .emacs file
;; To use this mode correctly place a recompile=NEVER in your config.php file
;; And be sure compile is not recursive
;; The PWB dir
;;(setq pwb-dir "/home/marian/workspace/pwb/")
;; The application path
;;(setq pwb-app-dir "/home/marian/workspace/eurekacozzuol/src/")
;; Debug mode
;;(setq pwb-verbose t)
;; Compile on save
;;(setq pwb-compile-after-saving t)
;;(setq pwb-compile-after-saving "prompt")
;; Save before compiling
;;(setq pwb-save-before-compiling t)
;;(setq pwb-save-before-compiling "prompt")
;; Recompile when config.php gets modified
;;(setq pwb-recompile-after-config-saving t)
;;(setq pwb-recompile-after-config-saving "prompt")
;;(require 'pwb)

(require 'cl)
(require 'php-mode)

;; -----------------------------------
(defvar pwb-verbose nil "Use debug-mode for compiling pwb files")
(defvar pwb-compile-after-saving nil "Compile after saving. Possible values: t, nil, or prompt")
(defvar pwb-save-before-compiling nil "Save the buffer before compiling. Possible values: t, nil, or prompt")
(defvar pwb-recompile-after-config-saving nil "Recompile the whole proyect after config.php gets modified")

(defun pwb-after-save-hook ()
  (if (equal (buffer-file-name) (concatenate 'string pwb-app-dir "config.php"))
      (pwb-recompile-after-config-saving)
    (pwb-compile-file-after-saving)))

(defun pwb-compile-file-after-saving ()
  (when pwb-compile-after-saving
      (if (equal pwb-compile-after-saving "prompt")
	  (let ((ans (read-from-minibuffer "Compile the file?(yes/no)")))
	    (unless (or (equal ans "n")
			(equal ans "no"))
	      (pwb-prim-compile-file)))
	(pwb-prim-compile-file))))

(defun pwb-recompile-after-config-saving ()
  (when pwb-recompile-after-config-saving
      (if (equal pwb-recompile-after-config-saving "prompt")
	  (let ((ans (read-from-minibuffer "Configuration was modified. Recompile the proyect?(yes/no)")))
	    (unless (or (equal ans "n")
			(equal ans "no"))
	      (pwb-prim-compile-all)))
	(pwb-prim-compile-all))))

;; Open pwb-mode on *.php files
(push '("\\.php\\'" . pwb-mode) auto-mode-alist)

;; The pwb-mode
(defvar pwb-buffer-mode nil "Is not nil if the current buffer is in pwb-mode")

(define-derived-mode pwb-mode
  php-mode "PWB"
  "Major mode for PHPWebBuilder development"
  ;; Mark the buffer as in pwb-mode
  (make-local-variable 'pwb-buffer-mode)
  (setq pwb-buffer-mode t)
  (add-hook 'after-save-hook 'pwb-after-save-hook nil t))

;;(defun set-pwb-mode ()
;;  (setq pwb-buffer-mode t)
;;  (pwb-mode))


;; Key binding
(define-key pwb-mode-map (kbd "C-c C-k") 'pwb-compile-file)
(define-key pwb-mode-map (kbd "C-c C-a") 'pwb-compile-all)

;; The PWB Menu
(define-key pwb-mode-map [menu-bar PWB]
  (cons "PWB" (make-sparse-keymap "PWB")))

;; Compile file menu
(define-key pwb-mode-map [menu-bar PWB compile-file]
  '("Compile file" . pwb-compile-file))

;; Compile all menu
(define-key pwb-mode-map [menu-bar PWB compile-all]
  '("Compile all" . pwb-compile-all))

;; PHP Scripts
(defvar pwb-compiler-command (concatenate 'string pwb-dir "scripts/compile-file.php") "Path to script for compiling files")
(defvar pwb-compile-all-command (concatenate 'string pwb-dir "scripts/compile-all.php") "Path to script for compiling the proyect")

;; Compiles a file
(defun pwb-compile-file ()
  (interactive)
  (when pwb-save-before-compiling
    (when (buffer-modified-p)
      (if (equal pwb-save-before-compiling "prompt")
	  (let ((ans (read-from-minibuffer "Save before compiling?(yes/no): ")))
	    (unless (or (equal ans "n")
			(equal ans "no"))
	      (without-local-hooks '((after-save-hook pwb-after-save-hook))
				   (basic-save-buffer))))
	(without-local-hooks '((after-save-hook pwb-after-save-hook))
			     (basic-save-buffer)))))
  (pwb-prim-compile-file))

(defun pwb-compile-all ()
  (interactive)
  ;; TODO: look for modified pwb-buffers and ask for saving
  (pwb-prim-compile-all))

(defun pwb-prim-compile-file ()
  (shell-command (concatenate 'string "php-cgi " pwb-compiler-command
			      " app_dir=" pwb-app-dir
			      " file=" (buffer-file-name)
			      (if pwb-verbose " verbose=yes" ""))))

(defun pwb-prim-compile-all ()
  ;;(shell-command (concatenate 'string "php-cgi " pwb-app-dir "Action.php recompile=yes")))
  (shell-command (concatenate 'string "php-cgi " pwb-compile-all-command
			      " app_dir=" pwb-app-dir
			      (if pwb-verbose " verbose=yes" ""))))

;; TODO: improve this macro
(defmacro without-local-hooks (hooks &rest body)
  (let ((hook (gensym))
	(function (gensym))
	(hook-and-function (gensym)))
    `(progn (dolist (,hook-and-function ,hooks)
	      (destructuring-bind (,hook ,function) ,hook-and-function
		(assert (remove-hook ,hook ,function t))))
	    ,@body
	    (dolist (,hook-and-function ,hooks)
	      (destructuring-bind (,hook ,function) ,hook-and-function
		(add-hook ,hook ,function nil t))))))

(provide 'pwb)