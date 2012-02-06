Cliche is an Image Gallery Component for MODx Revolution 2.2. 

Recomendation
---------------

Cliche's files were refactored since last version.
If you've installed the previous version, uninstall it before installing this update, or remove manually the directories in {core_directory}/components/cliche and {assets_directory}/components/cliche 
Note that if you want to keep your existing albums, you have to not remove the directories albums and cache located in {assets_directory}/components/cliche

Installation
---------------

- Install the component via the Package Manager.
- Reload the page
- Select Cliche under Components main menu
- Create an Album and upload some images
- Use the Cliche snippet to display your Albums

For the TV:

- Create a new Template Variable
- Give the name of your choice
- On the "Input Options" tab, set input type to : "clichethumbnail" 
- On the "Ouput Options" tab, set output type to : "clichethumbnail" 
- Adjust the setting to your preference on the "Output Options" tab
- Do not modify the "Template Access" tab yet
- Save your new TV
- Once the page has been reloaded, you can modify the "Template Access" tab to your convenience
- Use your TV to set a thumbnail

* Each TV who use the clichethumbnail type have a reserved album that you can see but not modify on the CMP
* If you change the name of your TV, the reserved album name will change accordingly
* If you remove the TV, the album will not be erased but it's "type" will fall back to default (internally) and therefore be editable like any other albums
* You can use several instance of thumbnail TV on one resource

Features
-----------

- Simple Image Management : The cmp focuses on simplicity 
- Multiple File Upload : Batch upload of images and/or zip upload are available for your convenience
- Easy to template: You can use the html markup that you want (either in a tpl file or in a chunk via the manager) along with any CSS style that you need
- Javascript Effect : Use any popular image effect with the library of your choice

Requirements
------------------

- You need to install "Cliche" in order to use this TV