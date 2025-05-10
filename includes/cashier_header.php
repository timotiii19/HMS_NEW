<!-- header.php -->

<style>
    .header {
        display: flex;
        position: fixed;
        justify-content: space-between;
        align-items: center;
        width: 98%;
        background-color: #eb6d9b;
        padding: 10px 20px;
        color: white;
        height: 60px;
        z-index: 10;
    }

    .content {
    margin-left: 220px;
    padding: 90px 20px 20px 20px; /* Top padding accounts for header */
    box-sizing: border-box;
    }

    .left-section,
    .right-section {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .logo {
        height: 40px;
    }

    .menu-icon {
        font-size: 20px;
        cursor: pointer;
    }

    .search-section {
        display: flex;
        align-items: center;
        background: #fcc0ef;
        border-radius: 20px;
        padding: 5px 10px;
    }

    .search-section input {
        background: transparent;
        border: none;
        outline: none;
        color: white;
        padding: 5px;
        width: 200px;
    }

    .search-icon {
        margin-left: 5px;
        color: #cc8383;
    }

    .avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        border: 2px solid #4caf50;
    }

    .user-dropdown {
    display: flex;
    align-items: center;
    font-size: 14px;
    color: white;
    
</style>


<div class="header">
    <div class="left-section">
        <img src="../../images/hosplogo.png" alt="Logo" class="logo">
        <i class="fas fa-bars menu-icon"></i>
        <div class="dropdown">
        </div>
    </div>

    <div class="search-section">
        <input type="text" placeholder="Search...">
        <i class="fas fa-search search-icon"></i>
    </div>

    <div class="right-section">
        <img src="../../assets/user.png" alt="Avatar" class="avatar">
        <div class="user-dropdown">
            <span>System Administrator <i class="fas fa-chevron-down"></i></span>
        </div>
    </div>
</div>