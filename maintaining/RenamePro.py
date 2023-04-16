import os

# Get user input for find and replace strings
find_str = input("Enter the string to find: ")
replace_str = input("Enter the string to replace with: ")

# Get user input for folder path
image_directory = input("Enter the path to your image folder: ")

# Loop through all files in the folder
for filename in os.listdir(image_directory):
    # Check if the filename contains the find string
    if find_str in filename:
        # Replace the find string with the replace string and create a new filename
        new_filename = filename.replace(find_str, replace_str)

        # Create the full paths for the old and new filenames
        old_path = os.path.join(image_directory, filename)
        new_path = os.path.join(image_directory, new_filename)

        # Rename the file
        os.rename(old_path, new_path)
        
