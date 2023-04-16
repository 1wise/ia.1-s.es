import os
import sys
import getch

# Function to get user input without waiting for Enter key
def get_input():
    user_input = ''
    while True:
        char = getch.getch()
        if char == '\n' or char == '\r':
            break
        user_input += char
    return user_input

# Prompt user for find and replace strings
print("Enter the string to find:")
find_string = get_input()
print("Enter the string to replace it with:")
replace_string = get_input()

# Prompt user for path to image directory
print("Enter the path to the image directory:")
image_directory = input()

# Rename files in image directory
for filename in os.listdir(image_directory):
    # Check if the filename contains the find string
    if find_string in filename:
        # Replace the find string with the replace string
        new_filename = filename.replace(find_string, replace_string)

        # Create the full paths for the old and new filenames
        old_path = os.path.join(image_directory, filename)
        new_path = os.path.join(image_directory, new_filename)

        # Rename the file
        os.rename(old_path, new_path)
