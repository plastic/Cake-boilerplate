require 'rubygems'
require 'capcake'

set :application, "cake-boilerplate"
set :repository,  File.expand_path(File.join(File.dirname(__FILE__), '..', '..', '..'))
set :scm, :git

# Deploy settings
set :deploy_via, :copy
set :copy_exclude, [".git/*", ".git", ".gitignore"]
set :copy_compression, :gzip

# Options
set :use_sudo, false
set :keep_releases, 10

# Roles & servers
role :app, "cake-boilerplate", :primary => true
set :user, "cake-boilerplate"

role :web, "your web-server here"                          # Your HTTP server, Apache/etc
# role :app, "your app-server here"                          # This may be the same as your `Web` server
role :db,  "your primary db-server here", :primary => true # This is where Rails migrations will run
role :db,  "your slave db-server here"

#set :deploy_to, "/var/www/vhosts/hellowworld.tld/#{application}"
#set :document_root, "/var/www/vhosts/hellowworld.tld/httpdocs/current"

# Repository Settings
# set :scm_username, "cap" #if http
# set :scm_password, "fHw1n71d" #if http
# set :scm_checkout, "export" 

# SSH Settings
# set :user, "clz"
# set :password, "password"
# set :use_sudo, false
# set :ssh_options, {:forward_agent => true}

# If you are using Passenger mod_rails uncomment this:
# if you're still using the script/reapear helper you will need
# these http://github.com/rails/irs_process_scripts

# namespace :deploy do
#   task :start do ; end
#   task :stop do ; end
#   task :restart, :roles => :app, :except => { :no_release => true } do
#     run "#{try_sudo} touch #{File.join(current_path,'tmp','restart.txt')}"
#   end
# end